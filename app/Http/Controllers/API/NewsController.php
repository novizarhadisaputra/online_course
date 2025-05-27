<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Resources\CommentResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Course\StoreCommentRequest;
use App\Http\Resources\UserResource;

class NewsController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $news = News::active()->paginate($request->input('limit', 10));
            if ($request->input('search')) {
                $news = $news->where('name', 'like', '%' . $request->input('search') . '%');
            }
            return $this->success(data: NewsResource::collection($news), paginate: $news);
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
            $news = News::find($id);
            if (!$news) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'news id'])]);
            }
            return $this->success(data: new NewsResource($news));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function likes(Request $request, string $id)
    {
        try {
            $news = News::find($id);
            if (!$news) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'news id'])]);
            }
            $likes = $news->likes()->paginate($request->input('limit', 10));
            return $this->success(data: UserResource::collection($likes), paginate: $likes);
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
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'news id'])]);
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
    public function storeComment(StoreCommentRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $news = News::find($id);
            if (!$news) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'news id'])]);
            }
            $transaction = $news->transactions()->where('user_id', $request->user_id)->first();

            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
            }

            $comment = $news->comments()->create([
                'description' => $request->description,
                'user_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->success(data: new CommentResource($comment), status: 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLike(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $news = News::find($id);
            if (!$news) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'news id'])]);
            }

            $request->user()->likeNews()->toggle($news->id);

            DB::commit();
            $news = News::find($id);
            return $this->success(data: new NewsResource($news), status: 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
