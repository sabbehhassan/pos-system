<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// ✅ SHARED WIDGETS (IMPORT)
use App\Filament\Shared\Widgets\{
    WelcomeWidget,
    DashboardStats,
    SalesChartWidget,
    LowStockWidget,
    RecentSalesWidget
};

class ManagerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('manager')
            ->path('manager')
            ->login(false)
            ->authGuard('manager')

            ->viteTheme('resources/css/filament/manager/theme.css')

            ->discoverPages(
                in: app_path('Filament/Manager/Pages'),
                for: 'App\\Filament\\Manager\\Pages'
            )

            ->discoverResources(
                in: app_path('Filament/Manager/Resources'),
                for: 'App\\Filament\\Manager\\Resources'
            )

            // ✅ EXPLICIT WIDGET REGISTRATION (MANDATORY)
            ->widgets([
                WelcomeWidget::class,
                DashboardStats::class,
                SalesChartWidget::class,
                LowStockWidget::class,
                RecentSalesWidget::class,
            ])

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
            ]);
    }
}