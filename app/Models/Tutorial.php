<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tutorial extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get the parent model .
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
