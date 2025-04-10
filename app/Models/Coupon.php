<?php

namespace App\Models;

use App\Models\Couponable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Coupon extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', Couponable::class);
    }

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'model', Couponable::class);
    }

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', Couponable::class);
    }
}
