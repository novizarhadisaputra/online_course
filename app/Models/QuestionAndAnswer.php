<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionAndAnswerCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAndAnswer extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get the category that owns the QuestionAndAnswer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionAndAnswerCategory::class, 'question_and_answer_category_id', 'id');
    }
}
