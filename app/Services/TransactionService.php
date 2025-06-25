<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Event;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Enums\TransactionStatus;
use App\Models\TransactionDetail;

class TransactionService
{
    public static function enrollmentProcess(Transaction $transaction)
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
                if ($transaction->status == TransactionStatus::SUCCESS->value) {
                    self::removeItemFromCart($item);
                }
            }
        }
    }

    public static function removeItemFromCart(TransactionDetail $item)
    {
        Cart::where(['model_id' => $item->model_id, 'model_type' => $item->model_type])->delete();
    }
}
