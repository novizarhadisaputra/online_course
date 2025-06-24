<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tutorial extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Get the parent model .
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
