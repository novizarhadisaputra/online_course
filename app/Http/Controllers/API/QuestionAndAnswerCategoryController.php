<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionAndAnswerCategoryResource;
use App\Models\QuestionAndAnswerCategory;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class QuestionAndAnswerCategoryController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $questionAndAnswerCategories = QuestionAndAnswerCategory::active()->paginate($request->input('limit', 10));
            return $this->success(data: QuestionAndAnswerCategoryResource::collection($questionAndAnswerCategories), paginate: $questionAndAnswerCategories);
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
