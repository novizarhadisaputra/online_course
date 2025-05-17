<?php

namespace App\Models;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quiz extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the quiz's options.
     */
    public function options(): MorphMany
    {
        return $this->morphMany(Option::class, 'model');
    }

    /**
     * Get the parent model (anything).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
