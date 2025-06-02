<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelHasBranch extends Model
{
    /**
     * Get the parent model (user or etc).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
