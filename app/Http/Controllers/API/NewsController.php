<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCommentRequest;
use App\Http\Requests\Course\StoreReviewRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\ReviewResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $news = News::active()->paginate($request->input('limit', 10));
            return $this->success(data: NewsResource::collection($news), paginate: $news);
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
            $news = News::find($id);
            if (!$news) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'new id'])]);
            }
            $comments = $news->comments()->paginate($request->input('limit', 10));
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
    public function storeComment(StoreCommentRequest $request, string $id)
    {
        $news = News::find($id);
        if (!$news) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'new id'])]);
        }
        $transaction = $news->transactions()->where('user_id', $request->user_id)->first();

        if (!$transaction) {
            throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
        }

        $comment = $news->comments()->create([
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        return $this->success(data: new CommentResource($comment), status: 201);
    }
}
