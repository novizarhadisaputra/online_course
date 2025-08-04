<?php

namespace App\Observers;

use App\Models\Province;
use Illuminate\Support\Str;

class ProvinceObserver
{
    /**
     * Handle the Province "creating" event.
     */
    public function creating(Province $province): void
    {
        $province->slug = Str::slug($province->name);
    }

    /**
     * Handle the Province "updating" event.
     */
    public function updating(Province $province): void
    {
        $province->slug = Str::slug($province->name);
    }

    /**
     * Handle the Province "deleted" event.
     */
    public function deleted(Province $province): void
    {
        //
    }

    /**
     * Handle the Province "restored" event.
     */
    public function restored(Province $province): void
    {
        //
    }

    /**
     * Handle the Province "force deleted" event.
     */
    public function forceDeleted(Province $province): void
    {
        //
    }
}
