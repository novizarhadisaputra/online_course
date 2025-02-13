<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'taggable', Taggable::class);
    }
}
