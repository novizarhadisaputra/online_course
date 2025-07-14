<?php

namespace App\Services;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Models\PaymentGateway;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendTransactionStatusEmailJob;

class IpaymuService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected ?string $va = "",
        protected ?string $apiKey = "",
        protected ?string $url = "",
    ) {
        $this->getCredentials();
    }

    public function initiate(): PaymentGateway
    {
        $paymentGateway = PaymentGateway::where('name', 'ilike', 'ipaymu')->first();
        if (!$paymentGateway) {
            throw new Exception(message: "credential doesn't exists");
        }
        return $paymentGateway;
    }

    protected function getCredentials()
    {
        $paymentGateway = $this->initiate();

        $this->va = $paymentGateway->configs['virtual_account'];
        $this->apiKey = $paymentGateway->configs['client_key'];
        $this->url = $paymentGateway->configs['base_url'];
    }

    /**
     * Get list of available payment channels.
     */
    public function getPaymentChannels(): array|string
    {
        $url = $this->url . '/api/v2/payment-channels';
        $method = 'GET';
        $requestBody = strtolower(hash('sha256', '{}'));
        $signature = $this->generateSignature($method, $requestBody);
        $timestamp = now()->format('YmdHis');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'va' => $this->va,
            'signature' => $signature,
            'timestamp' => $timestamp,
        ])->get($url);

        return $response->successful() ? $response->json() : $response->body();
    }

    /**
     * Make a payment request to iPaymu.
     */
    public function makePayment(Request $request, Transaction $transaction): array|string
    {
        $url = $this->url . '/api/v2/payment/direct';
        $method = 'POST';

        $products = [];
        $qty = [];
        $price = [];

        foreach ($transaction->details as $detail) {
            $products[] = $detail->model->name;
            $qty[] = $detail->qty;
            $price[] = $detail->price;
        }

        if ($transaction->service_fee) {
            $products[] = 'Service Fee';
            $qty[] = 1;
            $price[] =  $transaction->service_fee;
        }

        if ($transaction->tax_fee) {
            $products[] = 'Tax Fee';
            $qty[] = 1;
            $price[] =  $transaction->tax_fee;
        }

        $payload = [
            'name' => $transaction->user->name,
            'phone' => $transaction->user->phone,
            'email' =>  $transaction->user->email,
            'amount' => $transaction->total_price + $transaction->service_fee + $transaction->tax_fee,
            'notifyUrl' => route('api.webhooks.payments.post.receive-from-payment', ['gateway' => 'ipaymu']),
            'expired' => '24',
            'expiredType' => 'hours',
            'referenceId' => $transaction->code,
            'paymentMethod' => $transaction->payment_method->payment_channel->configs['code'],
            'paymentChannel' => $transaction->payment_method->configs['code'],
            'product' => $products,
            'qty' => $qty,
            'price' => $price,
        ];

        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $requestBody = strtolower(hash('sha256', $jsonBody));
        $signature = $this->generateSignature($method, $requestBody);
        $timestamp = now()->format('YmdHis');

        ThirdPartyLog::create([
            'name' => 'xendit',
            'event_name' => 'create payment request',
            'ip_address' => $request->ip(),
            'data' => $payload,
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'va' => $this->va,
            'signature' => $signature,
            'timestamp' => $timestamp,
        ])->post($url, $payload);
        $encoded = json_encode($response->json());
        $result = json_decode($encoded);

        $data = null;
        switch ($transaction->payment_method->payment_channel->configs['code']) {
            case 'va':
                $data = [
                    'id' => $result->Data->TransactionId,
                    'reference_id' => $result->Data->ReferenceId,
                    'customer_name' => $transaction->user->name,
                    'virtual_account_number' => $result->Data->PaymentNo,
                    'expires_at' => $result->Data->Expired,
                ];
                break;
            case 'qris':
                $data = [
                    'id' => $result->Data->TransactionId,
                    'reference_id' => $result->Data->ReferenceId,
                    'customer_name' => $transaction->user->name,
                    'qr_string' => $result->Data->PaymentNo,
                    'expires_at' => $result->Data->Expired,
                ];
                break;
            case 'cc':
                $data = [
                    'id' => $result->Data->TransactionId,
                    'reference_id' => $result->Data->ReferenceId,
                    'customer_name' => $transaction->user->name,
                    'expires_at' => $result->Data->Expired,
                ];
                $transaction->payment_link = $result->Data->Url;
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

    /**
     * Generate signature for request.
     */
    protected function generateSignature(string $method, string $requestBody): string
    {
        $stringToSign = strtoupper($method) . ':' . $this->va . ':' . $requestBody . ':' . $this->apiKey;
        return hash_hmac('sha256', $stringToSign, $this->apiKey);
    }

    public function receiveFromHook(mixed $receive_data, Transaction $transaction)
    {
        try {
            if ($transaction->status == TransactionStatus::WAITING_PAYMENT->value) {
                if ($receive_data && $receive_data->status) {
                    if ($receive_data->status === 'berhasil') {
                        $transaction->status = 'success';
                    } else if ($receive_data->status === 'expired') {
                        $transaction->status = 'expire';
                    } else if ($receive_data->status === 'pending') {
                        $transaction->status = 'pending';
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
