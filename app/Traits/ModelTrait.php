<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\ModelHasBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait ModelTrait
{
    /**
     * Scope a query to only include active users.
     */

    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    public function branches(): MorphToMany
    {
        return $this->morphToMany(Branch::class, 'model', ModelHasBranch::class);
    }
}
