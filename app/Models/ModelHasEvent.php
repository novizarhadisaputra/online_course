<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasEvent extends Model
{
    public function model()
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
