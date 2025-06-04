<?php

namespace App\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteExpiredNotificationTokens
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $report = Arr::get($event->data, 'report');

        $target = $report->target();

        $event->notifiable->deviceTokens()
            ->where('token`', $target->value())
            ->delete();
    }
}
