<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Enums\TransactionStatus;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Transaction\StoreEnrollmentEventRequest;
use App\Http\Requests\Transaction\StoreEnrollmentCourseRequest;

class EnrollmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function storeEnrollmentCourse(StoreEnrollmentCourseRequest $request)
    {
        try {
            DB::beginTransaction();

            $course = Course::find($request->course_id);
            if (!$course) {
                throw ValidationException::withMessages(['course_id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $transaction_code = Str::upper(Str::random(10));
            $existCode = Transaction::where('code', $transaction_code)->exists();
            while ($existCode) {
                $transaction_code = Str::upper(Str::random(10));
                $existCode = Transaction::where('code', $transaction_code)->exists();
            }

            $transaction = Transaction::create([
                'code' => $transaction_code,
                'service_fee' => 0,
                'tax_percentage' => 0,
                'status' => TransactionStatus::WAITING_PAYMENT,
                'category' => TransactionCategory::DEBIT,
                'user_id' => $request->user()->id,
            ]);

            $total_qty = 0;
            $total_price = 0;

            $price = $course->price;
            $transaction->details()->create([
                'model_id' => $course->id,
                'model_type' => Course::class,
                'qty' => 1,
                'units' => $price ? $price->units : 'courses',
                'price' => $price ? $price->value : 0,
            ]);

            $total_price += $price->value;

            $transaction->address_id = $request->address_id ?? null;
            $transaction->total_qty = $total_qty;
            $transaction->tax_fee = 0;
            $transaction->service_fee = 0;
            $transaction->total_price = $total_price;
            $transaction->status = $total_price > 0 ? TransactionStatus::WAITING_PAYMENT : TransactionStatus::SUCCESS;
            $transaction->save();

            if ($transaction->status == TransactionStatus::SUCCESS) {
                TransactionService::enrollmentProcess($transaction);
            }

            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeEnrollmentEvent(StoreEnrollmentEventRequest $request)
    {
        try {
            DB::beginTransaction();

            $event = Event::find($request->event_id);
            if (!$event) {
                throw ValidationException::withMessages(['event_id' => trans('validation.exists', ['attribute' => 'event id'])]);
            }

            $transaction_code = Str::upper(Str::random(10));
            $existCode = Transaction::where('code', $transaction_code)->exists();
            while ($existCode) {
                $transaction_code = Str::upper(Str::random(10));
                $existCode = Transaction::where('code', $transaction_code)->exists();
            }

            $transaction = Transaction::create([
                'code' => $transaction_code,
                'service_fee' => 0,
                'tax_percentage' => 0,
                'status' => TransactionStatus::WAITING_PAYMENT,
                'category' => TransactionCategory::DEBIT,
                'user_id' => $request->user()->id,
            ]);

            $total_qty = 0;
            $total_price = 0;

            $price = $event->price;
            $transaction->details()->create([
                'model_id' => $event->id,
                'model_type' => Event::class,
                'qty' => 1,
                'units' => $price ? $price->units : 'events',
                'price' => $price ? $price->value : 0,
            ]);

            $total_price += $price->value;

            $transaction->address_id = $request->address_id ?? null;
            $transaction->total_qty = $total_qty;
            $transaction->tax_fee = 0;
            $transaction->service_fee = 0;
            $transaction->total_price = $total_price;
            $transaction->status = $total_price > 0 ? TransactionStatus::WAITING_PAYMENT : TransactionStatus::SUCCESS;
            $transaction->save();

            if ($transaction->status == TransactionStatus::SUCCESS) {
                TransactionService::enrollmentProcess($transaction);
            }

            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
