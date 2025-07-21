<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        $user->first_name = preg_replace('/\s+/', ' ', $user->first_name);
        $user->last_name = preg_replace('/\s+/', ' ', $user->last_name);
        $user->name = trim($user->first_name . ($user->last_name ? ' ' . $user->last_name : ''));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void {}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
