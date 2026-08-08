@php
    $isLoggedIn = auth()->check();
    $isLoginRoute = request()->routeIs('*.login') || str_contains(request()->url(), '/login');
@endphp

@if($isLoggedIn && !$isLoginRoute)
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoNombre = $tenant?->nombre ?? 'edificio';
        $currentUrl = request()->url();

        // RUTAS DIRECTAS A LAS PANTALLAS HUB DE CATEGORÍAS
        try { $urlEscritorio = \App\Filament\Vecino\Pages\Escritorio::getUrl(); } catch (\Throwable $e) { $urlEscritorio = "/vecino/edificio/{$condoNombre}/escritorio"; }
        try { $urlFinanzas = \App\Filament\Vecino\Pages\FinanzasHub::getUrl(); } catch (\Throwable $e) { $urlFinanzas = "/vecino/edificio/{$condoNombre}/finanzas-hub"; }
        try { $urlSeguridad = \App\Filament\Vecino\Pages\SeguridadHub::getUrl(); } catch (\Throwable $e) { $urlSeguridad = "/vecino/edificio/{$condoNombre}/seguridad-hub"; }
        try { $urlGestion = \App\Filament\Vecino\Pages\GestionHub::getUrl(); } catch (\Throwable $e) { $urlGestion = "/vecino/edificio/{$condoNombre}/gestion-hub"; }
        try { $urlComunidad = \App\Filament\Vecino\Pages\ComunidadHub::getUrl(); } catch (\Throwable $e) { $urlComunidad = "/vecino/edificio/{$condoNombre}/comunidad-hub"; }
    @endphp

    <style>
        /* BARRA INFERIOR FIJA MÓVIL ESTILO APP NATIVA */
        .livo-mobile-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 99999 !important;
            padding: 0.65rem 0.5rem;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -10px 25px rgba(0,0,0,0.3);
        }

        html:not(.dark) .livo-mobile-navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            border-top: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 -10px 25px rgba(0,0,0,0.08) !important;
        }

        .livo-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            transition: all 0.2s;
        }

        html:not(.dark) .livo-nav-item { color: #64748b; }

        .livo-nav-item.active,
        .livo-nav-item:hover {
            color: #0284c7 !important;
        }

        /* OCULTAR EN ESCRITORIO */
        @media (min-width: 768px) {
            .livo-mobile-navbar { display: none !important; }
        }

        /* OCULTAR HAMBURGUESA Y CENTRAR LOGO EN CELULARES */
        @media (max-width: 768px) {
            .fi-sidebar-open-button,
            button[x-on\:click*="sidebar"] {
                display: none !important;
            }

            .fi-topbar .fi-logo,
            header.fi-topbar .fi-logo,
            .fi-sidebar-header {
                margin: 0 auto !important;
                justify-content: center !important;
                text-align: center !important;
            }

            .fi-main {
                padding-bottom: 5.5rem !important;
            }
        }
    </style>

    <div class="livo-mobile-navbar">
        <a href="{{ $urlEscritorio }}" class="livo-nav-item {{ str_contains($currentUrl, 'escritorio') ? 'active' : '' }}">
            <span style="font-size: 1.25rem;">🏠</span>
            <span>Escritorio</span>
        </a>

        <a href="{{ $urlFinanzas }}" class="livo-nav-item {{ str_contains($currentUrl, 'finanzas') ? 'active' : '' }}">
            <span style="font-size: 1.25rem;">💰</span>
            <span>Finanzas</span>
        </a>

        <a href="{{ $urlSeguridad }}" class="livo-nav-item {{ str_contains($currentUrl, 'seguridad') ? 'active' : '' }}">
            <span style="font-size: 1.25rem;">🛡️</span>
            <span>Seguridad</span>
        </a>

        <a href="{{ $urlGestion }}" class="livo-nav-item {{ str_contains($currentUrl, 'gestion') ? 'active' : '' }}">
            <span style="font-size: 1.25rem;">⚙️</span>
            <span>Gestión</span>
        </a>

        <a href="{{ $urlComunidad }}" class="livo-nav-item {{ str_contains($currentUrl, 'comunidad') ? 'active' : '' }}">
            <span style="font-size: 1.25rem;">👥</span>
            <span>Comunidad</span>
        </a>
    </div>
@endif