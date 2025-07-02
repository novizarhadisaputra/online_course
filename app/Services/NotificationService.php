<?php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class NotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function broadcast(User $user, ?string $title = 'Saved successfully', ?string $description = "")
    {
        $user->notify(
            Notification::make()
                ->title($title)
                ->body($description)
                ->actions([
                    Action::make('view')
                        ->button()
                        ->markAsRead(),
                ])
                ->toDatabase(),
        );
    }
}
