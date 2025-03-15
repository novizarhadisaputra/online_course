<?php

namespace App\Models;

use App\Models\PaymentMethod;
use App\Models\PaymentChannel;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PaymentGateway extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'configs' => 'array',
    ];

    /**
     * Get all of the channels for the PaymentGateway
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function channels(): HasMany
    {
        return $this->hasMany(PaymentChannel::class);
    }

    /**
     * Get all of the methods for the PaymentGateway
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function methods(): HasManyThrough
    {
        return $this->hasManyThrough(PaymentMethod::class, PaymentChannel::class);
    }
}
