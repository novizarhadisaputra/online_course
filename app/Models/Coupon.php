<?php

namespace App\Models;

use App\Models\Couponable;
use App\Models\CouponUsage;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Coupon extends Model
{
    use HasUuids, ModelTrait;

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

    /**
     * Get all of the usage courses that are assigned this coupon.
     */
    public function usage_courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', CouponUsage::class);
    }

    /**
     * Get all of the usage categories that are assigned this coupon.
     */
    public function usage_categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'model', CouponUsage::class);
    }

    /**
     * Get all of the usage users that are assigned this coupon.
     */
    public function usage_users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', CouponUsage::class);
    }
}
