<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\InstructorResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Course\StoreCommentRequest;
use App\Http\Resources\UserResource;

class InstructorController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $instructor = User::role('instructor')->paginate($request->input('limit', 10));
            return $this->success(data: InstructorResource::collection($instructor), paginate: $instructor);
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
            $instructor = User::find($id);
            if (!$instructor) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'instructor id'])]);
            }
            return $this->success(data: new InstructorResource($instructor));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function followers(Request $request, string $id)
    {
        try {
            $instructor = User::find($id);
            if (!$instructor) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'instructor id'])]);
            }
            $followers = $instructor->followers()->paginate($request->input('limit', 10));
            return $this->success(data: UserResource::collection($followers), paginate: $followers);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeFollower(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $instructor = User::find($id);
            if (!$instructor) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'instructor id'])]);
            }

            $request->user()->following()->toggle($instructor->id);

            DB::commit();
            $instructor = User::find($id);
            return $this->success(data: new InstructorResource($instructor), status: 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
