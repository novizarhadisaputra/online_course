<?php

namespace App\Http\Controllers\API;

use App\Models\PrivateClass;
use App\Models\Cart;
use App\Models\ConfigApp;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateClassResource;
use App\Http\Resources\CartResource;
use App\Http\Requests\PrivateClass\StoreCartRequest;
use Illuminate\Validation\ValidationException;

class PrivateClassController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of private classes.
     */
    public function index(Request $request)
    {
        try {
            $query = PrivateClass::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }

            if ($request->filled('is_paid')) {
                $query->where('is_paid', $request->boolean('is_paid'));
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Load relationships
            $query->with(['price', 'metadata', 'courses']);

            $privateClasses = $query->paginate($request->input('limit', 10));

            return $this->success(
                data: PrivateClassResource::collection($privateClasses),
                paginate: $privateClasses,
                message: __('api.private_class.list_success')
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified private class.
     */
    public function show(Request $request, string $slug)
    {
        try {
            $privateClass = PrivateClass::where('slug', $slug)
                ->with([
                    'price',
                    'metadata',
                    'courses' => function ($query) {
                        $query->where('status', true)
                              ->with(['price', 'metadata', 'user:id,name']);
                    },
                    'items' => function ($query) {
                        $query->orderBy('order', 'asc');
                    }
                ])
                ->first();

            if (!$privateClass) {
                throw ValidationException::withMessages([
                    'slug' => __('api.private_class.not_found')
                ]);
            }

            return $this->success(
                data: new PrivateClassResource($privateClass),
                message: __('api.private_class.detail_success')
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Add private class to cart.
     */
    public function addToCart(StoreCartRequest $request)
    {
        DB::beginTransaction();
        try {
            $privateClass = PrivateClass::find($request->id);
            if (!$privateClass) {
                throw ValidationException::withMessages([
                    'id' => __('api.private_class.not_found')
                ]);
            }

            if (!$privateClass->status) {
                throw ValidationException::withMessages([
                    'id' => __('api.private_class.inactive')
                ]);
            }

            $config_app = ConfigApp::first();
            if (!$config_app) {
                throw ValidationException::withMessages([
                    'id' => __('api.messages.error')
                ]);
            }

            // Check if private class already exists in cart
            $cart = Cart::where([
                'model_id' => $request->id,
                'model_type' => PrivateClass::class,
                'user_id' => $request->user()->id
            ])->first();

            if ($cart) {
                // Update existing cart item
                $cart->qty = $request->qty;
                $cart->price_id = $request->price_id;
                $cart->save();
            } else {
                // Create new cart item
                $cart = Cart::create([
                    'model_id' => $request->id,
                    'model_type' => PrivateClass::class,
                    'qty' => $request->qty,
                    'tax_fee' => $config_app->tax_fee,
                    'price_id' => $request->price_id,
                    'user_id' => $request->user()->id,
                ]);
            }

            DB::commit();
            return $this->success(
                data: new CartResource($cart),
                message: __('api.private_class.add_to_cart_success')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}