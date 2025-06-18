<?php

namespace App\Models;

use App\Models\Note;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Address extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];

    /**
     * Get all of the address's note.
     */
    public function note(): MorphOne
    {
        return $this->morphOne(Note::class, 'priceable');
    }
}
