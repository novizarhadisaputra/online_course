<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ads extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
