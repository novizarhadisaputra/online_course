<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateClassItem extends Model
{
    use HasUuids, ModelTrait;

    protected $fillable = [
        'private_class_id',
        'model_type',
        'model_id',
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function privateClass(): BelongsTo
    {
        return $this->belongsTo(PrivateClass::class, 'private_class_id', 'id');
    }
}