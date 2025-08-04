<?php

namespace App\Observers;

use App\Models\Village;
use Illuminate\Support\Str;

class VillageObserver
{
    /**
     * Handle the Village "creating" event.
     */
    public function creating(Village $village): void
    {
        $village->slug = Str::slug($village->name);
    }

    /**
     * Handle the Village "updating" event.
     */
    public function updating(Village $village): void
    {
        $village->slug = Str::slug($village->name);
    }

    /**
     * Handle the Village "deleted" event.
     */
    public function deleted(Village $village): void
    {
        //
    }

    /**
     * Handle the Village "restored" event.
     */
    public function restored(Village $village): void
    {
        //
    }

    /**
     * Handle the Village "force deleted" event.
     */
    public function forceDeleted(Village $village): void
    {
        //
    }
}
