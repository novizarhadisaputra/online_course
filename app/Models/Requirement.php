<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];
}
