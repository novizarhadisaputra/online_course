<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Jobs\SendTransactionStatusEmailJob;
use App\Models\ThirdPartyLog;
use Xendit\Configuration;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Xendit\PaymentRequest\PaymentRequestApi;
use Illuminate\Validation\ValidationException;
use Xendit\PaymentMethod\PaymentMethodParameters;
use Xendit\PaymentRequest\PaymentRequestParameters;

class XenditService
{
    protected $transaction;
    protected $config;
    protected PaymentMethodParameters $paymentMethodParams;

    /**
     * Create a new class instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function createTransaction(Request $request)
    {
        try {
            if ($this->transaction->payment_method->payment_channel && $this->transaction->payment_method->payment_channel->payment_gateway) {
                if ($this->transaction->payment_method->payment_channel->payment_gateway->configs) {
                    $configs = $this->transaction->payment_method->payment_channel->payment_gateway->configs;
                    if (!$configs['api_key']) {
                        throw ValidationException::withMessages(['config' => trans('validation.exists', ['attribute' => 'Config API Key'])]);
                    }
                    $this->config = Configuration::setXenditKey($configs['api_key']);
                }
            }

            $apiInstance = new PaymentRequestApi();
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
            } else if ($payment_channel && Str::slug(Str::lower($payment_channel->name), '') === 'ewallet') {
                $params['payment_method']['type'] = 'EWALLET';
                $params['payment_method']['ewallet'] = [
                    'channel_code' => $payment_method->configs['code'],
                    'channel_properties' => [
                        'customer_name' => $this->transaction->user->name,
                        'expires_at' => now()->addDay(),
                        'success_return_url' => $this->transaction->payment_method->configs['success_return_url'],
                        'failure_return_url' => $this->transaction->payment_method->configs['failure_return_url'],
                        'cancel_return_url' => $this->transaction->payment_method->configs['cancel_return_url']
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

            ThirdPartyLog::create([
                'name' => 'xendit',
                'event_name' => 'create payment request',
                'ip_address' => $request->ip(),
                'data' => $params,
            ]);

            $payment_request_parameters = new PaymentRequestParameters($params);
            $response_payment_request = $apiInstance->createPaymentRequest(null, null, null, $payment_request_parameters);

            ThirdPartyLog::create([
                'name' => 'xendit',
                'event_name' => 'response create payment request',
                'ip_address' => null,
                'data' => $response_payment_request,
            ]);

            Log::info(json_encode($response_payment_request));

            $result = json_decode(json_encode($response_payment_request));
            if ($result->payment_method && $result->payment_method->virtual_account && $result->payment_method->virtual_account->channel_properties) {
                $data = [
                    'id' => $result->payment_method->id,
                    'reference_id' => $result->payment_method->reference_id,
                    'customer_name' => $result->payment_method->virtual_account->channel_properties->customer_name,
                    'virtual_account_number' => $result->payment_method->virtual_account->channel_properties->virtual_account_number,
                    'expires_at' => $result->payment_method->virtual_account->channel_properties->expires_at,
                ];
                $this->transaction->data = $data;
            } else if ($result->payment_method && $result->payment_method->qr_code && $result->payment_method->qr_code->channel_properties) {
                $data = [
                    'id' => $result->payment_method->id,
                    'reference_id' => $result->payment_method->reference_id,
                    'qr_string' => $result->payment_method->qr_code->channel_properties->qr_string,
                ];
                $this->transaction->payment_link = $data['qr_string'];
                $this->transaction->data = $data;
            } else if ($result->payment_method && $result->payment_method->ewallet && $result->payment_method->ewallet->channel_properties) {
                $data = [
                    'id' => $result->payment_method->id,
                    'reference_id' => $result->payment_method->reference_id,
                    'payment_link' => $result->actions[0]->url,
                    'qr_string' => $result->actions[1]->qr_code
                ];
                $this->transaction->payment_link = $data['payment_link'];
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

    public function receiveFromHook(Request $request)
    {
        try {
            $receive_data = json_decode(json_encode($request->input()));

            if ($receive_data->event && $this->transaction->status == TransactionStatus::WAITING_PAYMENT->value) {
                if (Str::contains($receive_data->event, 'payment.')) {
                    if ($receive_data->data && $receive_data->data->status) {
                        if ($receive_data->data->status === 'SUCCEEDED') {
                            $this->transaction->status = 'success';
                        } else if ($receive_data->data->status === 'EXPIRED') {
                            $this->transaction->status = 'expire';
                        } else if ($receive_data->data->status === 'CANCELLED') {
                            $this->transaction->status = 'cancel';
                        } else if ($receive_data->data->status === 'PENDING') {
                            $this->transaction->status = 'pending';
                        } else if ($receive_data->data->status === 'FAILED') {
                            $this->transaction->status = 'fail';
                        }
                        $this->transaction->logs()->create([
                            'payment_method_id' => $this->transaction->payment_method_id,
                            'total_qty' => $this->transaction->total_qty,
                            'total_price' => $this->transaction->total_price,
                            'status' => $this->transaction->status,
                        ]);

                        SendTransactionStatusEmailJob::dispatch($this->transaction);

                        $this->transaction->save();
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
