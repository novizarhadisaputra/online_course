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
                if (!$configs['XENDIT_API_KEY']) {
                    throw ValidationException::withMessages(['config' => trans('validation.exists', ['attribute' => 'Config API Key'])]);
                }
                $this->config = Configuration::setXenditKey($configs['XENDIT_API_KEY']);
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
            $config_app = ConfigApp::first();
            if (!$config_app) {
                throw ValidationException::withMessages(['id' => 'please contact admin']);
            }

            $total_price = $this->transaction->total_price;
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
            if ($config_app->service_fee) {
                $service_fee = $config_app->service_fee;
                array_push($items, [
                    'reference_id' => $config_app->id,
                    'name' => 'Service Fee',
                    'type' => 'Fee',
                    'category' => 'Fee',
                    'currency' => 'IDR',
                    'quantity' => 1,
                    'price' => $service_fee,
                ]);
            }
            if ($config_app->tax_fee) {
                $tax_fee = ($total_price * $config_app->tax_fee) / 100;
                array_push($items, [
                    'reference_id' => $config_app->id,
                    'name' => 'Tax Fee',
                    'type' => 'Fee',
                    'category' => 'Fee',
                    'currency' => 'IDR',
                    'quantity' => 1,
                    'price' => $tax_fee,
                ]);
            }

            $payment_method = $this->transaction->payment_method;
            $params = [
                'reference_id' => $this->transaction->code,
                'currency' => 'IDR',
                'amount' => $this->transaction->total_price,
                'country' => 'IDR',
                'metadata' => [
                    'sku' => "transaction-sku-" . $this->transaction->id,
                ],
            ];
            $payment_channel = $payment_method->payment_channel;
            if ($payment_channel && Str::lower($payment_channel->name) === 'virtual account') {
                $params['payment_method'] = [
                    'type' => 'VIRTUAL_ACCOUNT',
                    'reusability' => 'ONE_TIME_USE',
                    'reference_id' => $this->transaction->id,
                    'virtual_account' => [
                        'channel_code' => $payment_method->configs['code'],
                        'channel_properties' => [
                            'customer_name' => $this->transaction->user->name,
                            'expires_at' => now()->addDay()
                        ]
                    ]
                ];
            }
            $params['items'] = $items;

            $payment_request_parameters = new PaymentRequestParameters($params);
            $response_payment_request = $this->apiInstance->createPaymentRequest(null, null, null, $payment_request_parameters);
            Log::info(json_encode($response_payment_request));
            dd($response_payment_request);
            $result = json_decode(json_encode($response_payment_request));
            if ($result->payment_method && $result->payment_method->virtual_account && $result->payment_method->virtual_account->channel_properties) {
                $data = [
                    'customer_name' => $result->payment_method->virtual_account->channel_properties->customer_name,
                    'virtual_account_number' => $result->payment_method->virtual_account->channel_properties->virtual_account_number,
                ];
                $this->transaction->data = $data;
            }
            $this->transaction->save();
            return $this->transaction;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
