@php
    $isLoggedIn = auth()->check();
    $isLoginRoute = request()->routeIs('*.login') || str_contains(request()->url(), '/login');
@endphp

@if($isLoggedIn && !$isLoginRoute)
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoNombre = $tenant?->nombre ?? 'edificio';
        $currentUrl = request()->url();

        try { $urlEscritorio = \App\Filament\Vecino\Pages\Escritorio::getUrl(); } catch (\Throwable $e) { $urlEscritorio = "/vecino/edificio/{$condoNombre}/escritorio"; }
        try { $urlFinanzas = \App\Filament\Vecino\Pages\FinanzasHub::getUrl(); } catch (\Throwable $e) { $urlFinanzas = "/vecino/edificio/{$condoNombre}/finanzas-hub"; }
        try { $urlSeguridad = \App\Filament\Vecino\Pages\SeguridadHub::getUrl(); } catch (\Throwable $e) { $urlSeguridad = "/vecino/edificio/{$condoNombre}/seguridad-hub"; }
        try { $urlGestion = \App\Filament\Vecino\Pages\GestionHub::getUrl(); } catch (\Throwable $e) { $urlGestion = "/vecino/edificio/{$condoNombre}/gestion-hub"; }
        try { $urlComunidad = \App\Filament\Vecino\Pages\ComunidadHub::getUrl(); } catch (\Throwable $e) { $urlComunidad = "/vecino/edificio/{$condoNombre}/comunidad-hub"; }
    @endphp

    <style>
        .livo-mobile-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 99999 !important;
            padding: 0.75rem 0.5rem 1.25rem 0.5rem;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.4);
        }

        html:not(.dark) .livo-mobile-navbar {
            background: rgba(255, 255, 255, 0.96) !important;
            border-top: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.08) !important;
        }

        .livo-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            transition: all 0.15s ease;
        }

        html:not(.dark) .livo-nav-item { color: #64748b; }

        .livo-nav-item.active,
        .livo-nav-item:hover {
            color: #38bdf8 !important;
            transform: translateY(-2px);
        }

        @media (min-width: 768px) {
            .livo-mobile-navbar { display: none !important; }
        }

        /* AJUSTE DE ISLA DINÁMICA MÓVIL Y OCULTAR HAMBURGUESA */
        @media (max-width: 768px) {
            .fi-sidebar-open-button,
            button[x-on\:click*="sidebar"] {
                display: none !important;
            }

            .fi-topbar,
            header.fi-topbar,
            .fi-topbar-start,
            .fi-sidebar-header {
                padding-top: calc(env(safe-area-inset-top, 44px) + 8px) !important;
            }

            .fi-topbar .fi-logo,
            header.fi-topbar .fi-logo,
            .fi-sidebar-header {
                margin: 0 auto !important;
                justify-content: center !important;
                text-align: center !important;
            }

            .fi-main {
                padding-bottom: 6rem !important;
            }
        }
    </style>

    <div class="livo-mobile-navbar">
        <a href="{{ $urlEscritorio }}" wire:navigate class="livo-nav-item {{ str_contains($currentUrl, 'escritorio') ? 'active' : '' }}">
            <span style="font-size: 1.3rem;">🏠</span>
            <span>Escritorio</span>
        </a>

        <a href="{{ $urlFinanzas }}" wire:navigate class="livo-nav-item {{ str_contains($currentUrl, 'finanzas') ? 'active' : '' }}">
            <span style="font-size: 1.3rem;">💰</span>
            <span>Finanzas</span>
        </a>

        <a href="{{ $urlSeguridad }}" wire:navigate class="livo-nav-item {{ str_contains($currentUrl, 'seguridad') ? 'active' : '' }}">
            <span style="font-size: 1.3rem;">🛡️</span>
            <span>Seguridad</span>
        </a>

        <a href="{{ $urlGestion }}" wire:navigate class="livo-nav-item {{ str_contains($currentUrl, 'gestion') ? 'active' : '' }}">
            <span style="font-size: 1.3rem;">⚙️</span>
            <span>Gestión</span>
        </a>

        <a href="{{ $urlComunidad }}" wire:navigate class="livo-nav-item {{ str_contains($currentUrl, 'comunidad') ? 'active' : '' }}">
            <span style="font-size: 1.3rem;">👥</span>
            <span>Comunidad</span>
        </a>
    </div>
@endif