<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasUuids, ModelTrait;

    /**
     * Get the parent model (anything).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the Enrollment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the certificate associated with the Enrollment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function certificate(): MorphOne
    {
        return $this->morphOne(Certificate::class, 'model');
    }

    /**
     * Get the score associated with the Enrollment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function score(): MorphOne
    {
        return $this->morphOne(Score::class, 'model')->orderBy('created_at', 'desc');
    }
}
