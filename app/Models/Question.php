<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Question extends Model
{
    use HasUuids, ModelTrait;

    /**
     * Get the parent model (anything).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
