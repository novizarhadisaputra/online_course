<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $tags = Tag::active()->paginate($request->input('limit', 10));
            return $this->success(data: TagResource::collection($tags), paginate: $tags);
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
            $tag = Tag::find($id);
            if (!$tag) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'tag id'])]);
            }
            $courses = $tag->courses()->paginate($request->input('limit', 10));
            return $this->success(data: CourseResource::collection($courses), paginate: $courses);
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
            $tag = Tag::find($id);
            if (!$tag) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'tag id'])]);
            }
            return $this->success(data: new TagResource($tag));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
