<x-filament-panels::page>
    @php
        $urlAvisos = \App\Filament\Resources\ComunicadoResource::getUrl('index');
        $urlMarketplace = \App\Filament\Resources\AnuncioResource::getUrl('index');
        $urlVotaciones = \App\Filament\Resources\VotacionResource::getUrl('index');
        $urlDocumentos = \App\Filament\Resources\DocumentoResource::getUrl('index');
        $urlMascotas = \App\Filament\Resources\MascotaResource::getUrl('index');
        $urlReclamos = \App\Filament\Resources\ReclamoResource::getUrl('index');
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
            'icon' => '👥',
            'badge' => 'VIDA EN COMUNIDAD',
            'title' => 'Comunidad & Servicios del Condominio',
            'description' => 'Accede a los avisos oficiales, marketplace vecinal, votaciones y mascotas.',
            'actions' => null,
        ])

        <div class="livo-hub-grid">
            {{-- MURO DE AVISOS --}}
            <a href="{{ $urlAvisos }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(13, 148, 136, 0.2); color: #14b8a6; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    📢
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Muro de Avisos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Comunicados oficiales</div>
                </div>
            </a>

            {{-- MARKETPLACE --}}
            <a href="{{ $urlMarketplace }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🛒
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Marketplace Vecinal</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Compra y venta entre vecinos</div>
                </div>
            </a>

            {{-- VOTACIONES --}}
            <a href="{{ $urlVotaciones }}" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(56, 189, 248, 0.2); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🗳️
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Votaciones & Acuerdos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Decisiones de la junta</div>
                </div>
            </a>

            {{-- DOCUMENTOS --}}
            <a href="{{ $urlDocumentos }}" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(168, 85, 247, 0.2); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    📁
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Biblioteca de Documentos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Reglamentos y actas PDF</div>
                </div>
            </a>

            {{-- MASCOTAS --}}
            <a href="{{ $urlMascotas }}" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(236, 72, 153, 0.2); color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🐾
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Mis Mascotas</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Registro del padrón</div>
                </div>
            </a>

            {{-- RECLAMOS --}}
            <a href="{{ $urlReclamos }}" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(16, 185, 129, 0.2); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    💬
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Reclamos & Reportes</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Sugerencias a la junta</div>
                </div>
            </a>
        </div>
    </div>
</x-filament-panels::page>