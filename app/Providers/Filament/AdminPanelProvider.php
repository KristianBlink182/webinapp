<?php

namespace App\Providers\Filament;

use App\Models\Condominio;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
           ->domain(app()->isLocal() ? null : 'admin.livo.com.pe')
->path(app()->isLocal() ? 'admin' : '')
->login()
            ->tenant(Condominio::class, slugAttribute: 'nombre') 
            ->tenantRoutePrefix('edificio')
            ->brandName('LIVO Admin')
            ->font('Inter') 
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->spa() 
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::SevenExtraLarge) 

            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => \App\Filament\Pages\EditProfile::getUrl()),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class, 
                \App\Filament\Pages\EditProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
          ->widgets([
    \App\Filament\Widgets\BienvenidaAdminWidget::class,
    \App\Filament\Widgets\AlertaSOSWidget::class,
    \App\Filament\Widgets\AdminStats::class,
    \App\Filament\Widgets\EgresosChart::class,
    \App\Filament\Widgets\CamaraLobbyWidget::class,
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
            
            ->navigationGroups([
                NavigationGroup::make()->label('Estructura del Edificio'),
                NavigationGroup::make()->label('Finanzas'),
                NavigationGroup::make()->label('Contabilidad & Reportes'),
                NavigationGroup::make()->label('Seguridad'),
                NavigationGroup::make()->label('Comunidad'),
                NavigationGroup::make()->label('Mantenimiento & Equipos'),
                NavigationGroup::make()->label('Configuración'),
            ])

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<link rel="stylesheet" href="{{ asset(\'css/custom.css\') }}?v=' . time() . '">'),
            );
            
    }
    
}