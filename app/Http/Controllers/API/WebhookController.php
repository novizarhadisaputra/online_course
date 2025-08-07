<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Traits\ResponseTrait;
use App\Services\XenditService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Notifications\PaymentCallbackNotification;
use App\Services\IpaymuService;
use App\Services\MidtransService;
use App\Services\TransactionService;
use Illuminate\Validation\ValidationException;

class WebhookController extends Controller
{
    use ResponseTrait;

    public function receiveFromPayment(Request $request, string $gateway)
    {
        ThirdPartyLog::create([
            'name' => $gateway,
            'event_name' => 'webhook receive data',
            'ip_address' => $request->ip(),
            'data' => $request->input(),
        ]);

        DB::beginTransaction();
        try {
            $input = json_decode(json_encode($request->input()));
            $transaction = null;

            switch ($gateway) {
                case 'xendit':
                    $transaction = Transaction::where('data->reference_id', $input->data->payment_method->reference_id)->first();
                    if ($transaction) {
                        $xendit = new XenditService($transaction);
                        $xendit->receiveFromHook($request);
                    }
                    break;
                case 'ipaymu':
                    $transaction = Transaction::where('data->reference_id', $input->reference_id)->first();
                    if ($transaction) {
                        $ipaymu = new IpaymuService($transaction);
                        $ipaymu->receiveFromHook($input, $transaction);
                    }
                    break;
                case 'midtrans':
                    $transaction = Transaction::where('data->reference_id', $input->order_id)->first();
                    if ($transaction) {
                        $ipaymu = new MidtransService();
                        $ipaymu->receiveFromHook($input, $transaction);
                    }
                    break;
                default:
                    break;
            }

            if ($transaction) {
                TransactionService::enrollmentProcess($transaction);
                $data = [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                ];
                $transaction->user->notify((new PaymentCallbackNotification($data))->afterCommit());
            }

            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
