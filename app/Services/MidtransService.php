<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Models\PaymentGateway;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendTransactionStatusEmailJob;
use Exception;
use Midtrans\CoreApi;

class MidtransService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected ?string $apiKey = "",
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
    /**
     * Make a payment request to midtrans.
     */
    public function makePayment(Request $request, Transaction $transaction): array|string
    {
        $items = [];

        foreach ($transaction->details as $detail) {
            $products[] = $detail->model->name;
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
            'payment_type' => 'credit_card',
            'credit_card'  => array(
                'token_id'      => $token_id,
                'authentication' => true,
                //        'bank'          => 'bni', // optional to set acquiring bank
                //        'save_token_id' => true   // optional for one/two clicks feature
            ),
            'transaction_details' => $transaction_details,
            'item_details'        => $items,
            'customer_details'    => $customer_details
        ];

        ThirdPartyLog::create([
            'name' => 'xendit',
            'event_name' => 'create payment request',
            'ip_address' => $request->ip(),
            'data' => $payload,
        ]);

        $response = CoreApi::charge();
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
