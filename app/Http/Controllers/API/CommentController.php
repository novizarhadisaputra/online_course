<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Comment\StoreRequest;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $comments = Comment::active()->paginate($request->input('limit', 10));
            return $this->success(data: CommentResource::collection($comments), paginate: $comments);
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
            $comment = Comment::find($id);
            if (!$comment) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'comment id'])]);
            }
            $comments = $comment->comments()->paginate($request->input('limit', 10));
            return $this->success(data: CommentResource::collection($comments), paginate: $comments);
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
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $comment = Comment::find($request->id);
            $comment = $comment->comments()->create([
                'description' => $request->description,
                'user_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->success(data: new CommentResource($comment), status: 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
