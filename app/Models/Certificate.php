<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    /**
     * Get the parent model (course or etc).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
