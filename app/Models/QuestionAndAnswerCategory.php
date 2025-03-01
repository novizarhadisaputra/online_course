<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionAndAnswerCategory extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the question_answers for the QuestionAndAnswerCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function question_answers(): HasMany
    {
        return $this->hasMany(QuestionAndAnswer::class, 'question_and_answer_category_id', 'id');
    }
}
