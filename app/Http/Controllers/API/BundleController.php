<?php

namespace App\Http\Controllers\API;

use App\Models\Bundle;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Carbon;
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
    public function courses(Request $request, string $slug)
    {
        try {
            $bundling = Bundle::where('slug', $slug)->first();
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
    public function show(Request $request, string $slug)
    {
        try {
            $bundling = Bundle::where('slug', $slug)->first();
            if (!$bundling) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'bundling id'])]);
            }
            if ($request->user()) {
                $viewer = $bundling->viewers()
                    ->whereDate('created_at', Carbon::today())
                    ->where('user_id', $request->user()->id)
                    ->first();
                if (!$viewer) {
                    $bundling->viewers()->create([
                        'user_id' => $request->user()->id
                    ]);
                }
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
