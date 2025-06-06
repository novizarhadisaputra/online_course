<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Forum extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    /**
     * Get all of the community's posts.
     */
    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'model');
    }
}
