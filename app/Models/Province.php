<?php

namespace App\Models;

use App\Models\Regency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Get all of the regencies for the Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }

    /**
     * Get all of the districts for the Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function districts(): HasManyThrough
    {
        return $this->hasManyThrough(District::class, Regency::class);
    }
}
