<?php

namespace App\Http\Controllers\API;

use App\Models\Ads;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Resources\AdsResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AdsController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $ads = Ads::active()->paginate($request->input('limit', 10));
            return $this->success(data: AdsResource::collection($ads), paginate: $ads);
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
            $ads = Ads::active()->find($id);
            if (!$ads) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'ads id'])]);
            }
            return $this->success(data: new AdsResource($ads));
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
