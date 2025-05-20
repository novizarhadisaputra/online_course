<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BundleItem extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get the parent model (course or etc).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
