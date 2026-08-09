@php
    $isLoggedIn = auth()->check();
    $isLoginRoute = request()->routeIs('*.login') || str_contains(request()->url(), '/login');
@endphp

{{-- 1. SEPARADOR FÍSICO SUPERIOR PARA LA ISLA DINÁMICA DE IPHONE --}}
<div class="livo-ios-statusbar-spacer"></div>

{{-- 2. PORTADA DE CARGA SPLASH SCREEN DE 3.5 SEGUNDOS (SOLO 1 VEZ POR SESIÓN) --}}
@if($isLoggedIn && !$isLoginRoute)
    <div x-data="{ 
             showSplash: !sessionStorage.getItem('livo_splash_shown') 
         }" 
         x-init="
             if (showSplash) {
                 setTimeout(() => { 
                     showSplash = false; 
                     sessionStorage.setItem('livo_splash_shown', 'true'); 
                 }, 3500);
             }
         " 
         x-show="showSplash" 
         x-transition:leave="transition ease-in duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         style="position: fixed; inset: 0; z-index: 2147483647 !important; background: #060913 url('{{ asset('splash.png') }}') center/cover no-repeat;"
         x-cloak>
    </div>
@endif

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
            pointer-events: auto !important;
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
            pointer-events: auto !important;
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