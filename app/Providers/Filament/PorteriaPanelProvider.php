<?php

namespace App\Providers\Filament;

use App\Models\Condominio;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PorteriaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('porteria')
            ->path('porteria')
            ->domain(app()->isLocal() ? null : 'porteria.livo.com.pe')
->path(app()->isLocal() ? 'porteria' : '')
->login()
           ->userMenuItems([
    'profile' => \Filament\Navigation\MenuItem::make()->url(fn (): string => \App\Filament\Pages\EditProfile::getUrl()),
])
            ->tenant(Condominio::class, slugAttribute: 'nombre') 
            ->tenantRoutePrefix('edificio')
            ->brandName('LIVO Portería')
            ->font('Inter')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::SevenExtraLarge)

            ->discoverResources(in: app_path('Filament/Porteria/Resources'), for: 'App\\Filament\\Porteria\\Resources')
            ->discoverPages(in: app_path('Filament/Porteria/Pages'), for: 'App\\Filament\\Porteria\\Pages')
            ->pages([
    Pages\Dashboard::class,
    \App\Filament\Pages\EditProfile::class,
])

            // 🎯 SOLO UN WIDGET UNIFICADO AL 100% DE ANCHO
            ->widgets([
                \App\Filament\Porteria\Widgets\PorteriaCommandCenter::class,
            ])
          ->brandLogo(function () {
            $tenant = \Filament\Facades\Filament::getTenant();
            if ($tenant && !empty($tenant->logo_claro) && file_exists(storage_path('app/public/' . $tenant->logo_claro))) {
                return asset('storage/' . $tenant->logo_claro);
            }
            return asset('images/logo-light.png');
        })
        ->darkModeBrandLogo(function () {
            $tenant = \Filament\Facades\Filament::getTenant();
            if ($tenant && !empty($tenant->logo) && file_exists(storage_path('app/public/' . $tenant->logo))) {
                return asset('storage/' . $tenant->logo);
            }
            return asset('images/logo-dark.png');
        })
        ->brandLogoHeight('3.8rem')

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

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<link rel="stylesheet" href="{{ asset(\'css/custom.css\') }}?v=' . time() . '">'),
            );
    }
}