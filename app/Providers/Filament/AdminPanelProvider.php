<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Widgets;
use App\Models\Branch;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Rmsramos\Activitylog\ActivitylogPlugin;
use App\Filament\Pages\Tenancy\RegisterTeam;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Nuxtifyts\DashStackTheme\DashStackThemePlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;
use App\Filament\Resources\UserResource\Pages\EditProfile;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->spa()
            ->databaseTransactions()
            ->id('admin')
            ->path('admin')
            ->profile(EditProfile::class)
            ->login()
            // ->tenant(Branch::class, ownershipRelationship: 'branches')
            // ->tenantRegistration(RegisterTeam::class,)
            ->passwordReset()
            ->emailVerification()
            ->colors([])
            // ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins(
                [
                    FilamentShieldPlugin::make()
                        ->gridColumns([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3
                        ])
                        ->sectionColumnSpan(1)
                        ->checkboxListColumns([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 4,
                        ])
                        ->resourceCheckboxListColumns([
                            'default' => 1,
                            'sm' => 2,
                        ]),
                    FilamentFullCalendarPlugin::make(),
                    DashStackThemePlugin::make(),
                    ActivitylogPlugin::make(),
                    RenewPasswordPlugin::make()
                        ->passwordExpiresIn(days: 30)
                        ->forceRenewPassword()

                ]
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->sidebarCollapsibleOnDesktop()
            ->theme(asset('css/filament/admin/theme.css'));
    }
}
