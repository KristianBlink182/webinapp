<x-filament-panels::page>
    @php
        $urlInvitados = \App\Filament\Resources\VisitaResource::getUrl('index');
        $urlCamara = \App\Filament\Vecino\Pages\CamaraEnVivo::getUrl();
    @endphp

    <style>
        .livo-hub-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        @media (max-width: 600px) {
            .livo-hub-grid { grid-template-columns: 1fr; }
        }

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
            'icon' => '🛡️',
            'badge' => 'SEGURIDAD Y CONTROL DE ACCESOS',
            'title' => 'Seguridad del Edificio',
            'description' => 'Pre-autoriza invitados para la Portería y monitorea la cámara de seguridad en vivo.',
            'actions' => null,
        ])

        <div class="livo-hub-grid">
            <a href="{{ $urlInvitados }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(56, 189, 248, 0.2); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    👤
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Mis Invitados</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Pases de ingreso para Portería</div>
                </div>
            </a>

            <a href="{{ $urlCamara }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(16, 185, 129, 0.2); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🎥
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Cámara de Seguridad</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Transmisión en vivo de la puerta</div>
                </div>
            </a>
        </div>
    </div>
</x-filament-panels::page>