<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'configs' => 'array',
    ];

    /**
     * Get all of the payment_methods for the PaymentGateway
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment_methods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
