<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ads extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
