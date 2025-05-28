<?php

namespace App\Models;

use App\Models\BundleItem;
use App\Traits\ModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Bundle extends Model implements HasMedia
{
    use ModelTrait, HasUuids, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get all of the items for the Bundle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class, 'bundle_id', 'id');
    }

    /**
     * Get all of the courses that are assigned this coupon.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', BundleItem::class);
    }

    /**
     * Get all of the products that are assigned this coupon.
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'model', BundleItem::class);
    }

    /**
     * Get all of the bundle's price.
     */
    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }
}
