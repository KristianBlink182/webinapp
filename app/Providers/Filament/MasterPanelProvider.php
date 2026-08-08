<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\MaxWidth;

class MasterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('master')
            ->path('master')
            ->domain(app()->isLocal() ? null : 'master.livo.com.pe')
->path(app()->isLocal() ? 'master' : '')
->login()
            ->brandName('LIVO Master HQ')
            ->font('Inter')
            ->colors([
                'primary' => Color::Violet, // Tono Ejecutivo de Alto Nivel
            ])
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::SevenExtraLarge)
            ->brandName('LIVO Master HQ')

            ->discoverResources(in: app_path('Filament/Master/Resources'), for: 'App\\Filament\\Master\\Resources')
            ->discoverPages(in: app_path('Filament/Master/Pages'), for: 'App\\Filament\\Master\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Master/Widgets'), for: 'App\\Filament\\Master\\Widgets')
            ->widgets([
                \App\Filament\Master\Widgets\StatsOverview::class, // Widget Ejecutivo SaaS
                \App\Filament\Master\Widgets\SaaSQuickActions::class,
            ])
->brandLogo(function () {
    $tenant = \Filament\Facades\Filament::getTenant();
    if ($tenant && !empty($tenant->logo)) {
        return asset('storage/' . $tenant->logo);
    }
    return null;
})
->brandLogoHeight('2.5rem')

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