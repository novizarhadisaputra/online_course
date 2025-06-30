<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\SearchHistory;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchHistory\DestroyRequest;
use App\Http\Resources\SearchHistoryResource;
use App\Http\Requests\SearchHistory\ListRequest;
use Illuminate\Validation\ValidationException;

class SearchHistoryController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(ListRequest $request)
    {
        try {
            $search_histories = SearchHistory::where('client_token', $request->client_token)->paginate($request->input('limit', 10));
            return $this->success(data: SearchHistoryResource::collection($search_histories), paginate: $search_histories);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $search_history = SearchHistory::where('client_token', $request->client_token)->where('id', $id)->first();
            if (!$search_history) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $search_history->delete();
            return $this->success(data: null);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
