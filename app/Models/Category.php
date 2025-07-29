<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Couponable;
use App\Observers\CategoryObserver;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy([CategoryObserver::class])]
class Category extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    /**
     * Get all of the courses for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function coupons(): MorphToMany
    {
        return $this->morphToMany(Coupon::class, 'model', Couponable::class);
    }
}
