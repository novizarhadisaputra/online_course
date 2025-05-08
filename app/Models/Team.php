<?php

namespace App\Models;

use App\Models\TeamRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Team extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the users that are assigned this team.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', TeamRelation::class);
    }
}
