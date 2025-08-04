<?php

namespace App\Observers;

use App\Models\District;
use Illuminate\Support\Str;

class DistrictObserver
{
    /**
     * Handle the District "creating" event.
     */
    public function creating(District $district): void
    {
        $district->slug = Str::slug($district->name);
    }

    /**
     * Handle the District "updating" event.
     */
    public function updating(District $district): void
    {
        $district->slug = Str::slug($district->name);
    }

    /**
     * Handle the District "deleted" event.
     */
    public function deleted(District $district): void
    {
        //
    }

    /**
     * Handle the District "restored" event.
     */
    public function restored(District $district): void
    {
        //
    }

    /**
     * Handle the District "force deleted" event.
     */
    public function forceDeleted(District $district): void
    {
        //
    }
}
