<?php

namespace App\Models;

use App\Models\Quiz;
use App\Models\Section;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get the section that owns the Lesson
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get all of the lesson's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }

    /**
     * Get all of the lesson's quizzes.
     */
    public function quizzes(): MorphMany
    {
        return $this->morphMany(Quiz::class, 'model');
    }
}
