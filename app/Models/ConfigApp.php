<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ConfigApp extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];
}
