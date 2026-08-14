<?php

namespace App\Providers\Filament;

use App\Models\Condominio;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
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

class VecinoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vecino')
            ->path('')
            ->domain(str_contains(request()->getHost(), 'test') ? null : 'vecino.livo.com.pe')
            ->path(str_contains(request()->getHost(), 'test') ? 'vecino' : '')
            ->login()
            ->tenant(Condominio::class, slugAttribute: 'nombre')
            ->tenantRoutePrefix('edificio')
            ->brandName('LIVO Vecinos')
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

            // ACTIVAR NOTIFICACIONES POP-UP EN VIVO
            ->databaseNotifications()
            ->databaseNotificationsPolling('3s')

            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => \App\Filament\Pages\EditProfile::getUrl()),
            ])

            ->pages([
                \App\Filament\Vecino\Pages\Escritorio::class,
                \App\Filament\Pages\EditProfile::class,
            ])

            ->discoverResources(in: app_path('Filament/Vecino/Resources'), for: 'App\\Filament\\Vecino\\Resources')
            ->discoverPages(in: app_path('Filament/Vecino/Pages'), for: 'App\\Filament\\Vecino\\Pages')

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

            ->navigationGroups([
                NavigationGroup::make('Finanzas'),
                NavigationGroup::make('Seguridad'),
                NavigationGroup::make('Gestión de Espacios'),
                NavigationGroup::make('Comunidad'),
                NavigationGroup::make('Configuración'),
            ])

            // OCULTAR LOGO DUPLICADO EN LA BARRA SUPERIOR
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style>header.fi-topbar a.fi-logo, header.fi-topbar .fi-logo { display: none !important; }</style>'
            )

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="' . asset('css/custom.css') . '?v=' . time() . '">',
            )

            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view()->exists('filament.vecino.components.mobile-navbar')
                    ? Blade::render('@include("filament.vecino.components.mobile-navbar")')
                    : ''
            );
    }
}