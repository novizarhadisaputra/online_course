<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Taggable extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get the user that owns the Taggable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the parent taggable model (course or other).
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
