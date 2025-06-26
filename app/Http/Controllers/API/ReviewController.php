<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $reviews = Review::active()->paginate($request->input('limit', 10));
            return $this->success(data: ReviewResource::collection($reviews), paginate: $reviews);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $reviews = Review::find($id);
            if (!$reviews) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'reviews id'])]);
            }
            return $this->success(data: new ReviewResource($reviews));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
