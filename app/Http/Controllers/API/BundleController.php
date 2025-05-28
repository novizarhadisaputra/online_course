<?php

namespace App\Http\Controllers\API;

use App\Models\Bundle;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Http\Resources\CourseResource;
use Illuminate\Validation\ValidationException;

class BundleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $bundlings = Bundle::active()->paginate($request->input('limit', 10));
            return $this->success(data: BundleResource::collection($bundlings), paginate: $bundlings);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function courses(Request $request, string $id)
    {
        try {
            $bundling = Bundle::find($id);
            if (!$bundling) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'bundling id'])]);
            }
            $courses = $bundling->courses()->paginate($request->input('limit', 10));
            return $this->success(data: CourseResource::collection($courses), paginate: $courses);
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
    public function show(string $id)
    {
        try {
            $bundling = Bundle::find($id);
            if (!$bundling) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'bundling id'])]);
            }
            return $this->success(data: new BundleResource($bundling));
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
