<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasUuids, ModelTrait;

    protected $guarded = [];
}
