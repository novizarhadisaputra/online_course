<?php

namespace App\Models;

use App\Models\PaymentMethod;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentChannel extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get the gateway that owns the PaymentChannel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get all of the methods for the PaymentChannel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function methods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
