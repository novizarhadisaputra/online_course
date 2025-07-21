<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyLog extends Model
{
    use HasUuids;

    protected $casts = [
        'data' => 'array',
    ];
}
