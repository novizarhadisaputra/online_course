<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetInTouch\StoreRequest;
use App\Models\GetInTouch;
use App\Traits\ResponseTrait;

class GetInTouchController extends Controller
{
    use ResponseTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $get_in_touch = GetInTouch::create($request->validated());

            DB::commit();
            return $this->success(data: $get_in_touch, status: 201);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
