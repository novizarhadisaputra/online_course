<?php

namespace App\Observers;

use App\Models\Regency;
use Illuminate\Support\Str;

class RegencyObserver
{
    /**
     * Handle the Regency "creating" event.
     */
    public function creating(Regency $regency): void
    {
        $regency->slug = Str::slug($regency->name);
    }

    /**
     * Handle the Regency "updating" event.
     */
    public function updating(Regency $regency): void
    {
        $regency->slug = Str::slug($regency->name);
    }

    /**
     * Handle the Regency "deleted" event.
     */
    public function deleted(Regency $regency): void
    {
        //
    }

    /**
     * Handle the Regency "restored" event.
     */
    public function restored(Regency $regency): void
    {
        //
    }

    /**
     * Handle the Regency "force deleted" event.
     */
    public function forceDeleted(Regency $regency): void
    {
        //
    }
}
