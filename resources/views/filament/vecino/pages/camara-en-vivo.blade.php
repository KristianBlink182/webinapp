<x-filament-panels::page>
    <style>
        .livo-camera-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
        }
        .livo-video-container {
            position: relative;
            padding-bottom: 56.25%; /* Relación de aspecto 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 1rem;
            background: #000000;
            border: 1px solid #374151;
            margin-top: 1rem;
        }
        .livo-video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>

    <div style="font-family: system-ui, -apple-system, sans-serif;" class="space-y-6">

        <!-- 🏛️ CABECERA EJECUTIVA -->
        @include('filament.components.header-card', [
            'icon'        => '📹',
            'badge'       => 'Monitoreo en Vivo',
            'title'       => 'Cámara de Seguridad del Condominio',
            'description' => 'Transmisión en tiempo real de la puerta principal y áreas de acceso del edificio para los vecinos.',
            'actions'     => null,
        ])

        <!-- REPRODUCTOR DE VIDEO -->
        <div class="livo-camera-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📹</span> Transmisión de la Puerta Principal
                    </h2>
                    <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem;">
                        Monitoreo en tiempo real del área principal de {{ $condominio?->nombre ?? 'tu edificio' }}.
                    </p>
                </div>
                <div style="padding: 0.35rem 0.85rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; font-weight: 800; font-size: 0.75rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.35rem;">
                    <span style="width: 8px; height: 8px; background: #34d399; border-radius: 50%; display: inline-block;"></span>
                    <span>TRANSMISIÓN EN VIVO</span>
                </div>
            </div>

            @if(!empty($urlCamara))
                <div class="livo-video-container">
                    <iframe src="{{ $urlCamara }}" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
            @else
                <div style="padding: 3rem 1.5rem; text-align: center; background: #1f2937; border-radius: 1rem; margin-top: 1rem; border: 1px dashed #374151;">
                    <svg style="width: 48px; height: 48px; color: #6b7280; margin: 0 auto 0.75rem auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <h3 style="font-weight: 700; font-size: 1rem; color: #e5e7eb; margin: 0;">No hay cámara configurada</h3>
                    <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem;">
                        La administración de tu edificio aún no ha configurado el enlace de la cámara principal.
                    </p>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>