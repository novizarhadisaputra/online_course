<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Score extends Model
{
    use HasUuids, ModelTrait;

    protected $fillable = [
        'model_type',
        'model_id',
        'batches',
        'value',
        'is_graduated',
        'user_id',
    ];

    protected $casts = [
        'is_graduated' => 'boolean',
        'value' => 'float',
    ];

    /**
     * Get the model that owns the score (Course, Bundle, etc.)
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the score
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
