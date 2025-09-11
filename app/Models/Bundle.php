<?php

namespace App\Models;

use App\Models\User;
use App\Models\Price;
use App\Models\Course;
use App\Models\Product;
use App\Models\Metadata;
use App\Models\BundleItem;
use App\Observers\BundleObserver;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy([BundleObserver::class])]
class Bundle extends Model implements HasMedia
{
    use ModelTrait, HasUuids, InteractsWithMedia;

    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class);
    }

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', BundleItem::class);
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'model', BundleItem::class);
    }

    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    public function metadata(): MorphOne
    {
        return $this->morphOne(Metadata::class, 'model');
    }

    public function enrollments(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', Enrollment::class);
    }
}
