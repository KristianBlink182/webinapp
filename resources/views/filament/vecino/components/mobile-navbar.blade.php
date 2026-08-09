@php
    $isLoggedIn = auth()->check();
    $isLoginRoute = request()->routeIs('*.login') || str_contains(request()->url(), '/login');
@endphp

{{-- 1. SEPARADOR FÍSICO SUPERIOR PARA LA ISLA DINÁMICA DE IPHONE --}}
<div class="livo-ios-statusbar-spacer"></div>

{{-- 2. PORTADA DE CARGA SPLASH SCREEN DE 3 SEGUNDOS (FOTO COMPLETA EDIFICIOS) --}}
@if($isLoggedIn && !$isLoginRoute)
    <div x-data="{ showSplash: true }" 
         x-init="setTimeout(() => showSplash = false, 3000)" 
         x-show="showSplash" 
         x-transition:leave="transition ease-in duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         style="position: fixed; inset: 0; z-index: 99999; background: #060913 url('{{ asset('splash.png') }}') center/cover no-repeat; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: #ffffff;"
        <div style="background: rgba(6, 9, 19, 0.7); backdrop-filter: blur(8px); position: absolute; inset: 0;"></div>
        <div style="position: relative; z-index: 10; padding: 2rem;">
            <img src="{{ asset('favicon.ico') }}" alt="LIVO" style="width: 90px; height: 90px; margin: 0 auto 1.5rem auto; object-fit: contain;">
            <h1 style="font-size: 2.2rem; font-weight: 900; color: #ffffff; margin: 0; letter-spacing: 0.05em;">LIVO</h1>
            <p style="font-size: 1rem; color: #38bdf8; font-weight: 700; margin-top: 0.5rem;">Administración Inteligente para Edificios y Condominios</p>
        </div>
    </div>
@endif

@if($isLoggedIn && !$isLoginRoute)
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoNombre = $tenant?->nombre ?? 'edificio';
        $currentUrl = request()->url();

        $urlEscritorio = "/vecino/edificio/{$condoNombre}/escritorio";
        $urlFinanzas = "/vecino/edificio/{$condoNombre}/finanzas-hub";
        $urlSeguridad = "/vecino/edificio/{$condoNombre}/seguridad-hub";
        $urlGestion = "/vecino/edificio/{$condoNombre}/gestion-hub";
        $urlComunidad = "/vecino/edificio/{$condoNombre}/comunidad-hub";
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
            padding: 0.65rem 0.5rem 1.25rem 0.5rem;
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
            color: #0284c7 !important;
        }

        @media (min-width: 768px) {
            .livo-mobile-navbar { display: none !important; }
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