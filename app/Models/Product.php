<?php

namespace App\Models;

use App\Models\User;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\Bundle;
use App\Models\BundleItem;
use App\Traits\ModelTrait;
use App\Models\ProductCategory;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    protected $casts = [
        'meta' => 'array',
    ];

    public function product_category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    public function branches(): HasManyThrough
    {
        return $this->hasManyThrough(Stock::class, Branch::class);
    }

    public function bundles(): MorphToMany
    {
        return $this->morphToMany(Bundle::class, 'model', BundleItem::class);
    }
}
