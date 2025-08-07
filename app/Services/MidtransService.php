<?php

namespace App\Services;

use Exception;
use Midtrans\CoreApi;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Models\PaymentGateway;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendTransactionStatusEmailJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function initiate(): PaymentGateway
    {
        $paymentGateway = PaymentGateway::where('name', 'ilike', 'midtrans')->first();
        if (!$paymentGateway) {
            throw new Exception(message: "credential doesn't exists");
        }
        return $paymentGateway;
    }
    /**
     * Make a payment request to midtrans.
     */
    public function makePayment(Request $request, Transaction $transaction): array|string
    {
        $paymentGateway = $this->initiate();

        $items = [];
        $total_price = $transaction->total_price;

        foreach ($transaction->details as $detail) {
            $items[] = [
                "id" => $detail->model->id,
                "price" => $detail->price,
                "quantity" => $detail->qty,
                "name" => $detail->model->name,
            ];
        }

        if ($transaction->service_fee) {
            $total_price += $transaction->service_fee;
            $items[] = [
                "id" => Str::uuid(),
                "price" => $transaction->service_fee,
                "quantity" => 1,
                "name" => 'Service Fee',
            ];
        }

        if ($transaction->tax_fee) {
            $total_price += $transaction->tax_fee;
            $items[] = [
                "id" => Str::uuid(),
                "price" => $transaction->tax_fee,
                "quantity" => 1,
                "name" => 'Tax Fee',
            ];
        }

        $customer_details = [
            "email" => $transaction->user->email,
            "first_name" => $transaction->user->first_name,
            "last_name" => $transaction->user->last_name,
            "phone" => $transaction->user->phone,
        ];

        $transaction_details = [
            "order_id" => $transaction->code,
            "gross_amount" => $total_price
        ];

        $code = $transaction->payment_method->payment_channel->configs['code'];
        $channel = $transaction->payment_method->configs['code'];
        $callback_url = $transaction->payment_method->configs['callback_url'];

        $payload = [
            'payment_type' => $code,
            'transaction_details' => $transaction_details,
            'item_details'        => $items,
            'customer_details'    => $customer_details
        ];

        switch ($code) {
            case 'bank_transfer':
                $payload[$code] = [
                    'bank' => $channel,
                ];
                break;
            case 'ewallet':
                $payload['payment_type'] = $channel;
                $payload[$code] = [
                    'enable_callback' => true,
                    'enable_callback' => $callback_url
                ];
                break;
            case 'qris':
                $payload['payment_type'] = $channel;
                $payload[$code] = [
                    'enable_callback' => true,
                    'enable_callback' => $callback_url
                ];
                break;

            default:
                # code...
                break;
        }

        ThirdPartyLog::create([
            'name' => 'xendit',
            'event_name' => 'create payment request',
            'ip_address' => $request->ip(),
            'data' => $payload,
        ]);

        $url = $paymentGateway->configs['base_url'] . '/v2/charge';
        $encoded = json_encode($payload);
        $key = base64_encode($paymentGateway->configs['server_key'] . ":");
        Log::info("request url: $url");
        Log::info("request payload: $encoded");
        Log::info("request Authorization: Basic $key");
        $response = Http::withHeader(name: 'Authorization', value: "Basic $key")
            ->acceptJson()
            ->asJson()
            ->post($paymentGateway->configs['base_url'] . '/v2/charge', $payload);
        $encoded = json_encode($response->json());
        Log::info("response payload: $encoded");
        $result = json_decode($encoded);
        if ($result->status_code != "201") {
            throw new Exception($result->status_message);
        }
        $data = null;

        switch ($code) {
            case 'bank_transfer':
                $data = [
                    'id' => $result->transaction_id,
                    'reference_id' => $result->order_id,
                    'customer_name' => $transaction->user->name,
                    'virtual_account_number' => $result->va_numbers[0]->va_number,
                    'expires_at' => Carbon::parse($transaction->created_at)->addDay(),
                ];
                break;
            case 'qris':
                $data = [
                    'id' => $result->transaction_id,
                    'reference_id' => $result->order_id,
                    'customer_name' => $transaction->user->name,
                    'payment_link' => $result->actions[0]->url,
                    'expires_at' => Carbon::parse($transaction->created_at)->addDay(),
                ];
                break;
            case 'ewallet':
                $data = [
                    'id' => $result->transaction_id,
                    'reference_id' => $result->order_id,
                    'customer_name' => $transaction->user->name,
                    'payment_link' => $result->actions[1]->url,
                    'expires_at' => Carbon::parse($transaction->created_at)->addDay(),
                ];
                break;
            default:
                # code...
                break;
        }
        $transaction->data = $data;
        $transaction->save();

        ThirdPartyLog::create([
            'name' => 'ipaymu',
            'event_name' => 'response create payment direct',
            'ip_address' => null,
            'data' => $response->json(),
        ]);

        return $transaction;
    }

    public function receiveFromHook(mixed $receive_data, Transaction $transaction)
    {
        try {
            if ($transaction->status == TransactionStatus::WAITING_PAYMENT->value) {
                if ($receive_data && $receive_data->status) {
                    if ($receive_data->status === 'capture' || $receive_data->status === 'settlement') {
                        $transaction->status = 'success';
                    } else if ($receive_data->status === 'expire') {
                        $transaction->status = 'expire';
                    } else if ($receive_data->status === 'pending') {
                        $transaction->status = 'pending';
                    } else if ($receive_data->status === 'cancel') {
                        $transaction->status = 'cancel';
                    } else if ($receive_data->status === 'failure') {
                        $transaction->status = 'fail';
                    }
                    $transaction->logs()->create([
                        'payment_method_id' => $transaction->payment_method_id,
                        'total_qty' => $transaction->total_qty,
                        'total_price' => $transaction->total_price,
                        'status' => $transaction->status,
                    ]);

                    SendTransactionStatusEmailJob::dispatch($transaction);

                    $transaction->save();
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
