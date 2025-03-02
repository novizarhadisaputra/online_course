<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;

trait ModelTrait
{
    /**
     * Scope a query to only include active users.
     */

    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }
}
