<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BundleItem extends Model
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
}
