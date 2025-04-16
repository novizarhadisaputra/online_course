<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Price;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Enums\TransactionStatus;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\CheckoutRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Transaction\StoreRequest;
use App\Http\Resources\PaymentChannelResource;
use App\Models\PaymentChannel;
use App\Services\XenditService;

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
                    ->where(['model_id' => $cart->cartable_id, 'model_type' =>  $cart->cartable_type])->first();
                if ($detail) {
                    $detail->qty = $cart->qty;
                    $detail->units = $price ? $price->units : 'courses';
                    $detail->price = $price ? $price->value : 0;
                    $detail->save();
                } else {
                    $transaction->details()->create([
                        'model_id' => $cart->cartable_id,
                        'model_type' =>  $cart->cartable_type,
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
            $transaction->payment_method_id = $request->payment_method_id;
            $transaction->save();

            if ($transaction->payment_method->payment_channel && $transaction->payment_method->payment_channel->payment_gateway) {
                $name = Str::lower($transaction->payment_method->payment_channel->payment_gateway->name);
                if ($name === 'xendit') {
                    $xendit = new XenditService($transaction);
                    $xendit->createTransaction();
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
