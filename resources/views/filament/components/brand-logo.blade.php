@php
    $tenant = \Filament\Facades\Filament::getTenant();
    $hasCustomLogo = $tenant && !empty($tenant->logo) && file_exists(storage_path('app/public/' . $tenant->logo));
@endphp

<style>
    .livo-brand-word {
        color: #ffffff !important;
        font-weight: 900 !important;
        font-size: 1.4rem !important;
        letter-spacing: 0.05em !important;
    }
    html:not(.dark) .livo-brand-word {
        color: #0f172a !important;
    }
</style>

@if($hasCustomLogo)
    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" class="h-9 w-auto object-contain">
@else
    <div style="display: flex; align-items: center; gap: 0.6rem;">
        {{-- CUBO 3D AZUL NATURAL (SIN FILTROS) --}}
        <img src="{{ asset('favicon.ico') }}" alt="LIVO" style="height: 32px; width: 32px; object-fit: contain; filter: none !important;">
        
        {{-- PALABRA LIVO --}}
        <span class="livo-brand-word">LIVO</span>
    </div>
@endif