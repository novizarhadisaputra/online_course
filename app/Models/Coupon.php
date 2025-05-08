<?php

namespace App\Models;

use App\Models\Couponable;
use App\Traits\ModelTrait;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Coupon extends Model implements HasMedia
{
    use HasUuids, ModelTrait, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get all of the courses that are assigned this coupon.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', Couponable::class);
    }

    /**
     * Get all of the categories that are assigned this coupon.
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'model', Couponable::class);
    }

    /**
     * Get all of the users that are assigned this coupon.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', Couponable::class);
    }

    /**
     * Get all of the usages for the Coupon
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id', 'id');
    }
}
