<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Models\PaymentMethod;
use Spatie\Sluggable\HasSlug;
use App\Models\PaymentChannel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PaymentGateway extends Model implements HasMedia
{
    use HasUuids, HasSlug, ModelTrait, InteractsWithMedia;

    protected $casts = [
        'configs' => 'array',
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
     * Get all of the payment-channels for the PaymentGateway
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment_channels(): HasMany
    {
        return $this->hasMany(PaymentChannel::class);
    }

    /**
     * Get all of the methods for the PaymentGateway
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payment_methods(): HasManyThrough
    {
        return $this->hasManyThrough(PaymentMethod::class, PaymentChannel::class);
    }
}
