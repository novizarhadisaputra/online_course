<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ConfigApp;
use App\Models\Course;
use App\Models\Event;
use App\Models\News;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $carts = Cart::where('user_id', $request->user()->id)->paginate($request->input('limit', 10));
            return $this->success(data: CartResource::collection($carts), paginate: $carts);
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
        DB::beginTransaction();
        try {
            $model_type = Course::class;
            if ($request->category == 'events') {
                $model_type = Event::class;
                $events = Event::find($request->id);
                if (!$events) {
                    throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
                }
            } else if ($model_type == 'news') {
                $model_type = News::class;
                $news = News::find($request->id);
                if (!$news) {
                    throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
                }
            } else {
                $course = Course::find($request->id);
                if (!$course) {
                    throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
                }
            }

            $config_app = ConfigApp::first();
            if (!$config_app) {
                throw ValidationException::withMessages(['id' => 'please contact admin']);
            }

            $cart = Cart::where([
                'model_id' => $request->id,
                'model_type' => $model_type,
                'user_id' => $request->user()->id
            ])->first();

            if ($cart) {
                $cart->qty = $request->qty;
                $cart->price_id = $request->price_id;
                $cart->save();
            } else {
                $cart = Cart::create([
                    'model_id' => $request->id,
                    'model_type' => $model_type,
                    'qty' => $request->qty,
                    'tax_fee' => $config_app->tax_fee,
                    'price_id' => $request->price_id,
                    'user_id' => $request->user()->id,
                ]);
            }

            DB::commit();
            return $this->success(data: new CartResource($cart));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $cart = Cart::where(['user_id' => $request->user()->id, 'id' => $id])->first();
            if (!$cart) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            return $this->success(data: Cart::collection($cart));
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
    public function destroy(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $cart = Cart::where(['user_id' => $request->user()->id, 'id' => $id])->first();
            if (!$cart) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $cart->delete();
            $carts = Cart::where('user_id', $request->user()->id)->paginate($request->input('limit', 10));
            DB::commit();
            return $this->success(data: CartResource::collection($carts), paginate: $carts);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
