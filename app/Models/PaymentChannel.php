<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Models\PaymentMethod;
use Spatie\Sluggable\HasSlug;
use App\Models\PaymentGateway;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentChannel extends Model implements HasMedia
{
    use HasUuids, HasSlug, ModelTrait, InteractsWithMedia;

    protected $casts = [
        'configs' => 'array'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the payment_gateway that owns the PaymentChannel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /**
     * Get all of the payment_methods for the PaymentChannel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment_methods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
