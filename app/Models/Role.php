<?php

namespace App\Models;

use App\Models\Team;
use App\Models\TeamRelation;
use Spatie\Permission\Models\Role as ModelsRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends ModelsRole
{
    use HasUuids;

    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'model', TeamRelation::class);
    }

}
