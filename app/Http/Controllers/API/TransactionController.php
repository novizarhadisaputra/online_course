<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\User;
use App\Models\Price;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Couponable;
use App\Models\CouponUsage;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\PaymentChannel;
use App\Services\XenditService;
use App\Enums\TransactionStatus;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\PaymentChannelResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Transaction\StoreRequest;
use App\Http\Requests\Transaction\CheckoutRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TransactionController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $transactions = Transaction::where('user_id', $request->user()->id)->paginate($request->input('limit', 10));
            return $this->success(data: TransactionResource::collection($transactions), paginate: $transactions);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

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
            $carts = Cart::whereIn('id', $request->cart_ids)->where('user_id', $request->user()->id)->get();
            if (!count($carts)) {
                throw ValidationException::withMessages(['cart_ids' => trans('validation.exists', ['attribute' => 'cart id'])]);
            }
            foreach ($carts as $cart) {
                $price = Price::find($cart->price_id);
                if ($price) {
                    $total_price += $price->value;
                }
                $total_qty += $cart->qty;
                $detail = $transaction->details()
                    ->where(['model_id' => $cart->model_id, 'model_type' =>  $cart->model_type])->first();
                if ($detail) {
                    $detail->qty = $cart->qty;
                    $detail->units = $price ? $price->units : 'courses';
                    $detail->price = $price ? $price->value : 0;
                    $detail->save();
                } else {
                    $transaction->details()->create([
                        'model_id' => $cart->model_id,
                        'model_type' =>  $cart->model_type,
                        'qty' => $cart->qty,
                        'units' => $price ? $price->units : 'courses',
                        'price' => $price ? $price->value : 0,
                    ]);
                }
            }

            $transaction->total_qty = $total_qty;
            $transaction->tax_fee = 0;
            $transaction->service_fee = 0;
            $transaction->total_price = $total_price;
            $transaction->save();

            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function payment(CheckoutRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::find($id);
            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
            }

            $log_data = [
                'payment_method_id' => $request->payment_method_id,
                'total_qty' => $transaction->total_qty,
                'total_price' => $transaction->total_price,
                'status' => $transaction->status,
            ];

            if ($request->coupon_code) {
                $model_ids = $transaction->details()->pluck('model_id')->all();
                array_push($model_ids, $request->user()->id);
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                $couponable = Couponable::where('coupon_id', $coupon->id)->whereHasMorph('model', [Course::class, User::class], function (Builder $query) use ($model_ids) {
                    $query->whereIn('id', $model_ids);
                })->exists();
                if (!$couponable) {
                    throw ValidationException::withMessages(['coupon_code' => trans('validation.exists', ['attribute' => 'coupon code'])]);
                }

                $transaction->coupon_id = $coupon->id;
                $log_data['coupon_id'] = $coupon->id;
                $coupon_usages = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $request->user()->id)->count();
                if ($coupon_usages >= $coupon->max_usable_times) {
                    throw ValidationException::withMessages(['coupon_code' => trans('validation.exists', ['attribute' => 'coupon code'])]);
                }
                $coupon->usages()->create([
                    'user_id' => $request->user()->id,
                    'status' => true
                ]);
            }

            $transaction->payment_method_id = $request->payment_method_id;
            $transaction->save();


            $transaction->logs()->create($log_data);

            if ($transaction->payment_method->payment_channel && $transaction->payment_method->payment_channel->payment_gateway) {
                $name = Str::lower($transaction->payment_method->payment_channel->payment_gateway->name);
                if ($name === 'xendit') {
                    $xendit = new XenditService($transaction);
                    $xendit->createTransaction($request);
                }
            }

            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $transaction = Transaction::where(['user_id' => $request->user()->id, 'id' => $id])->first();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function paymentChannels(Request $request, string $id)
    {
        try {
            $transaction = Transaction::find($id);
            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
            }
            $paymentChannels = PaymentChannel::active()->paginate($request->input('limit', 10));
            return $this->success(data: PaymentChannelResource::collection($paymentChannels), paginate: $paymentChannels);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
