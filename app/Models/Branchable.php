<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Branchable extends Model
{
    /**
     * Get the parent model (user or etc).
     */
    public function model(): MorphTo
    {
        dd($this->morphTo());
        return $this->morphTo();
    }
}
