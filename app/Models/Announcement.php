<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Announcement extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
