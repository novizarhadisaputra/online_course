<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Spatie\Permission\Models\Role as ModelsRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends ModelsRole
{
    use HasUuids, ModelTrait;
}
