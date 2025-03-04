<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCommentRequest;
use App\Http\Requests\Course\StoreReviewRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ReviewResource;
use App\Models\Course;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $courses = Course::active()->paginate($request->input('limit', 10));
            return $this->success(data: CourseResource::collection($courses), paginate: $courses);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function reviews(Request $request, string $id)
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $reviews = $course->reviews()->paginate($request->input('limit', 10));
            return $this->success(data: ReviewResource::collection($reviews), paginate: $reviews);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function comments(Request $request, string $id)
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $comments = $course->comments()->paginate($request->input('limit', 10));
            return $this->success(data: CommentResource::collection($comments), paginate: $comments);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeReview(StoreReviewRequest $request, string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
        }

        $transaction = $course->transactions()->where('user_id', $request->user()->id)->first();

        if (!$transaction) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
        }

        $review = $course->reviews()->create([
            'rating' => $request->rating,
            'description' => $request->description,
            'transaction_id' => $transaction->id,
            'user_id' => $request->user()->id,
        ]);

        return $this->success(data: new ReviewResource($review), status: 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeComment(StoreCommentRequest $request, string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
        }
        $transaction = $course->transactions()->where('user_id', $request->user_id)->first();

        if (!$transaction) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
        }

        $comment = $course->comments()->create([
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        return $this->success(data: new CommentResource($comment), status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            return $this->success(data: new CourseResource($course));
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
