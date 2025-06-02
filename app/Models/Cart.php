<?php

namespace App\Models;

use App\Models\User;
use App\Models\Price;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];

    /**
     * Get the parent model (course or etc).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the price that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
}
