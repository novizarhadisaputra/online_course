<?php

namespace App\Observers;

use App\Models\Bundle;
use Illuminate\Support\Str;

class BundleObserver
{
    /**
     * Handle the Bundle "creating" event.
     */
    public function creating(Bundle $bundle): void
    {
        $bundle->slug = Str::slug($bundle->name);
    }

    /**
     * Handle the Bundle "created" event.
     */
    public function created(Bundle $bundle): void
    {
        //
    }

    /**
     * Handle the Bundle "updating" event.
     */
    public function updating(Bundle $bundle): void
    {
        $bundle->slug = Str::slug($bundle->name);
    }

    /**
     * Handle the Bundle "updated" event.
     */
    public function updated(Bundle $bundle): void
    {
        //
    }

    /**
     * Handle the Bundle "deleted" event.
     */
    public function deleted(Bundle $bundle): void
    {
        //
    }

    /**
     * Handle the Bundle "restored" event.
     */
    public function restored(Bundle $bundle): void
    {
        //
    }

    /**
     * Handle the Bundle "force deleted" event.
     */
    public function forceDeleted(Bundle $bundle): void
    {
        //
    }
}
