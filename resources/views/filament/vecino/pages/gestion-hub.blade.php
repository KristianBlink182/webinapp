<x-filament-panels::page>
    @php
        $urlReservas = \App\Filament\Resources\ReservaResource::getUrl('index');
    @endphp

    <style>
        .livo-hub-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 1.25rem;
            padding: 1.5rem;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        html:not(.dark) .livo-hub-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-hub-card div { color: #0f172a !important; }
    </style>

    <div class="space-y-6">
        @include('filament.components.header-card', [
            'icon' => '⚙️',
            'badge' => 'RESERVAS Y ESPACIOS',
            'title' => 'Gestión de Áreas Comunes',
            'description' => 'Reserva el área de Parrillas, SUM o Gimnasio comprobando disponibilidad en tiempo real.',
            'actions' => null,
        ])

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <a href="{{ $urlReservas }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(168, 85, 247, 0.2); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    📅
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Reserva de Áreas Comunes</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Parrillas, SUM y espacios</div>
                </div>
            </a>
        </div>
    </div>
</x-filament-panels::page>