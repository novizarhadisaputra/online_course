<?php

namespace App\Providers;

use App\Listeners\DeleteExpiredNotificationTokens;
use App\Models\Activity;
use Laravel\Sanctum\Sanctum;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\Events\NotificationFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        DB::listen(function ($query) {
            Log::info($query->sql);     // the query being executed
            Log::info($query->time);    // query time in milliseconds
        });

        Event::listen(
            NotificationFailed::class,
            DeleteExpiredNotificationTokens::class,
        );
    }
}
