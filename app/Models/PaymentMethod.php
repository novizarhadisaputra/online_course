<?php

namespace App\Models;

use App\Models\Tutorial;
use App\Traits\ModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasSlug;

class PaymentMethod extends Model implements HasMedia
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
     * Get the payment_channel that owns the PaymentMethod
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_channel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    /**
     * Get all of the payment methods's tutorials.
     */
    public function tutorials(): MorphMany
    {
        return $this->morphMany(Tutorial::class, 'model');
    }
}
