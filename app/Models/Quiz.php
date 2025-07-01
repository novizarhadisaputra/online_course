<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quiz extends Model implements HasMedia
{
    use HasUuids, ModelTrait, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get all of the quiz's options.
     */
    public function options(): MorphMany
    {
        return $this->morphMany(Option::class, 'model');
    }

    /**
     * Get the answer associated with the Quiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function answer(): MorphOne
    {
        return $this->morphOne(Answer::class, 'model');
    }

    /**
     * Get the parent model (anything).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
