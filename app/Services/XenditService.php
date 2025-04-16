<?php

namespace App\Services;

use App\Models\ConfigApp;
use Xendit\Configuration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Xendit\PaymentRequest\PaymentRequestApi;
use Illuminate\Validation\ValidationException;
use Xendit\PaymentMethod\PaymentMethodParameters;
use Xendit\PaymentRequest\PaymentRequestParameters;

class XenditService
{
    protected $transaction;
    protected $config;
    protected PaymentRequestApi $apiInstance;
    protected PaymentMethodParameters $paymentMethodParams;

    /**
     * Create a new class instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
        if ($transaction->payment_method->payment_channel && $transaction->payment_method->payment_channel->payment_gateway) {
            if ($transaction->payment_method->payment_channel->payment_gateway->configs) {
                $configs = $transaction->payment_method->payment_channel->payment_gateway->configs;
                if (!$configs['xendit_api_key']) {
                    throw ValidationException::withMessages(['config' => trans('validation.exists', ['attribute' => 'Config API Key'])]);
                }
                $this->config = Configuration::setXenditKey($configs['xendit_api_key']);
            }
        }
        $this->apiInstance = new PaymentRequestApi();
    }

    public function createTransaction()
    {
        try {
            if (!$this->transaction) {
                throw ValidationException::withMessages(['transaction_id' => trans('validation.exists', ['attribute' => 'payment method'])]);
            }

            $total_price = $this->transaction->total_price;
            $tax_fee = 0;
            $service_fee = 0;
            $items = [];
            foreach ($this->transaction->details as $i => $item) {
                $items[$i] = [
                    'reference_id' => $item->model_id,
                    'name' => $item->model->name,
                    'type' => $item->model_type,
                    'category' => $item->model_type,
                    'currency' => 'IDR',
                    'quantity' => $item->qty,
                    'price' => $item->price,
                ];
            }
            if ($this->transaction->payment_method->configs && $this->transaction->payment_method->configs['service_fee']) {
                $service_fee = $this->transaction->payment_method->configs['service_fee'];
                if ($this->transaction->payment_method->configs['service_fee_type'] === 'percent') {
                    $service_fee = ($this->transaction->payment_method->configs['service_fee'] * $total_price) / 100;
                }
                array_push($items, [
                    'reference_id' => $this->transaction->payment_method->id,
                    'name' => 'Service Fee',
                    'type' => 'Fee',
                    'category' => 'Fee',
                    'currency' => 'IDR',
                    'quantity' => 1,
                    'price' => $service_fee,
                ]);
            }
            if ($this->transaction->payment_method->configs && $this->transaction->payment_method->configs['tax_fee']) {
                $tax_fee = $this->transaction->payment_method->configs['tax_fee'];
                if ($this->transaction->payment_method->configs['tax_fee_type'] === 'percent') {
                    $tax_fee = ($this->transaction->payment_method->configs['tax_fee'] * $total_price) / 100;
                }
                array_push($items, [
                    'reference_id' => $this->transaction->payment_method->id,
                    'name' => 'Tax Fee',
                    'type' => 'Fee',
                    'category' => 'Fee',
                    'currency' => 'IDR',
                    'quantity' => 1,
                    'price' => $tax_fee,
                ]);
            }

            $payment_method = $this->transaction->payment_method;
            $payment_channel = $payment_method->payment_channel;
            $params = [
                'reference_id' => $this->transaction->code,
                'currency' => 'IDR',
                'country' => 'IDR',
                'metadata' => [
                    'sku' => "transaction-sku-" . $this->transaction->id,
                ],
                'payment_method' => [
                    'reusability' => 'ONE_TIME_USE',
                    'reference_id' => $this->transaction->id,
                ]
            ];
            if ($payment_channel && Str::slug(Str::lower($payment_channel->name), '_') === 'virtual_account') {
                $params['payment_method']['type'] = 'VIRTUAL_ACCOUNT';
                $params['payment_method']['virtual_account'] = [
                    'channel_code' => $payment_method->configs['code'],
                    'channel_properties' => [
                        'customer_name' => $this->transaction->user->name,
                        'expires_at' => now()->addDay()
                    ]
                ];
            } else if ($payment_channel && Str::slug(Str::lower($payment_channel->name), '_') === 'ewallet') {
                $params['payment_method']['type'] = 'EWALLET';
                $params['payment_method']['ewallet'] = [
                    'channel_code' => $payment_method->configs['code'],
                    'channel_properties' => [
                        'customer_name' => $this->transaction->user->name,
                        'expires_at' => now()->addDay(),
                        'channel_properties' => [
                            'success_return_url' => $this->transaction->payment_method->configs['success_return_url']
                        ]
                    ]
                ];
            } else if ($payment_channel && Str::slug(Str::lower($payment_channel->name), '_') === 'qr_code') {
                $params['payment_method']['type'] = 'QR_CODE';
                $params['payment_method']['qr_code'] = [
                    'channel_code' => $payment_method->configs['code'],
                    'channel_properties' => [
                        'customer_name' => $this->transaction->user->name,
                    ]
                ];
            }
            $params['items'] = $items;
            $params['amount'] = $total_price + $service_fee + $tax_fee;

            $payment_request_parameters = new PaymentRequestParameters($params);
            $response_payment_request = $this->apiInstance->createPaymentRequest(null, null, null, $payment_request_parameters);

            Log::info(json_encode($response_payment_request));

            $result = json_decode(json_encode($response_payment_request));
            if ($result->payment_method && $result->payment_method->virtual_account && $result->payment_method->virtual_account->channel_properties) {
                $data = [
                    'id' => $result->id,
                    'customer_name' => $result->payment_method->virtual_account->channel_properties->customer_name,
                    'virtual_account_number' => $result->payment_method->virtual_account->channel_properties->virtual_account_number,
                    'expires_at' => $result->payment_method->virtual_account->channel_properties->virtual_account_number,
                ];
                $this->transaction->data = $data;
            }

            $this->transaction->tax_fee = $tax_fee;
            $this->transaction->service_fee = $service_fee;
            $this->transaction->total_price = $total_price;
            $this->transaction->save();

            return $this->transaction;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
