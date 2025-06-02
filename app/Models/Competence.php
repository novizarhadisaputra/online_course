<?php

namespace App\Models;

use App\Models\News;
use App\Models\Course;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Competence extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the courses that are assigned this competence.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', ModelHasCompetence::class);
    }

    /**
     * Get all of the news that are assigned this competence.
     */
    public function news(): MorphToMany
    {
        return $this->morphedByMany(News::class, 'model', ModelHasCompetence::class);
    }

     /**
     * Get all of the events that are assigned this competence.
     */
    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'model', ModelHasCompetence::class);
    }
}
