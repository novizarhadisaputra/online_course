<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class CouponController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $coupons = Coupon::active()->whereHas('users', function (Builder $query) use ($request) {
                $query->where('model_id', $request->user()->id);
            })->paginate($request->input('limit', 10));
            return $this->success(data: CouponResource::collection($coupons), paginate: $coupons);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $coupon = Coupon::active()->where('id', $id)->whereHas('users', function (Builder $query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })->first();
            return $this->success(data: new CouponResource($coupon));
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
