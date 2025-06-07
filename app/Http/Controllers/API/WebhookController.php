<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Traits\ResponseTrait;
use App\Services\XenditService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Notifications\PaymentCallbackNotification;

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

        try {
            DB::beginTransaction();
            $input = json_decode(json_encode($request->input()));

            switch ($gateway) {
                case 'xendit':
                    $transaction = Transaction::where('code', $input->data->reference_id)->first();
                    if ($transaction) {
                        $xendit = new XenditService($transaction);
                        $xendit->receiveFromHook($request);
                    }
                    break;
                default:
                    break;
            }

            $this->enrollmentProcess($transaction);

            $data = [
                'id' => $transaction->id,
                'status' => $transaction->status,
            ];

            $transaction->user->notify(new PaymentCallbackNotification($data)->afterCommit());
            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function enrollmentProcess(Transaction $transaction)
    {
        if ($transaction && $transaction->details()->count()) {
            foreach ($transaction->details as $item) {
                if ($item->model_type == Course::class) {
                    $course = Course::find($item->model_id);
                    if ($course) {
                        $enrollment = $course->enrollments()->where('transaction_detail_id', $item->id)->first();
                        if (!$enrollment) {
                            $enrollment = $course->enrollments()->create([
                                'transaction_detail_id' => $item->id,
                                'user_id' => $transaction->user_id,
                            ]);
                        }
                    }
                } else if ($item == Event::class) {
                    $event = Event::find($item->model_id);
                    if ($event) {
                        $enrollment = $event->enrollments()->where('transaction_detail_id', $item->id)->first();
                        if (!$enrollment) {
                            $enrollment = $event->enrollments()->create([
                                'transaction_detail_id' => $item->id,
                                'user_id' => $transaction->user_id,
                            ]);
                        }
                        $appointment = $event->appointments()->where('transaction_detail_id', $item->id)->first();
                        if ($appointment) {
                            $appointment = $event->appointments()->create([
                                'code' => Str::upper(Str::random(10)),
                                'transaction_detail_id' => $item->id,
                                'user_id' => $transaction->user_id,
                                'created_by' => $event->user_id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
