<x-filament-panels::page>
    <style>
        .livo-escritorio-container {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #ffffff;
            padding-bottom: 5.5rem; /* ESPACIO PARA LA BARRA INFERIOR MÓVIL */
        }

        /* ANIMACIONES */
        @keyframes livo-glow-pulse {
            0% { box-shadow: 0 0 12px rgba(245, 158, 11, 0.4); border-color: #f59e0b; }
            50% { box-shadow: 0 0 25px rgba(245, 158, 11, 0.9); border-color: #fbbf24; }
            100% { box-shadow: 0 0 12px rgba(245, 158, 11, 0.4); border-color: #f59e0b; }
        }

        @keyframes livo-green-glow {
            0% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.4); border-color: #10b981; }
            50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.95); border-color: #34d399; }
            100% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.4); border-color: #10b981; }
        }

        .livo-card-green-active {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            border: 2px solid #10b981;
            border-radius: 1.25rem;
            padding: 1.5rem;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            animation: livo-green-glow 2s infinite ease-in-out;
        }

        .livo-paquete-bar {
            background: rgba(245, 158, 11, 0.1);
            border: 2px solid #f59e0b;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            animation: livo-glow-pulse 2s infinite ease-in-out;
        }

        .livo-sismo-bar {
            background: #7f1d1d;
            border: 2px solid #ef4444;
            box-shadow: 0 0 20px rgba(224, 36, 36, 0.8);
            border-radius: 1.25rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .livo-sismo-btn-bien {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff;
            font-weight: 800;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
        }

        .livo-sismo-btn-ayuda {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: #ffffff;
            font-weight: 800;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
        }

        .livo-top-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 956px) {
            .livo-top-grid { grid-template-columns: 1fr; }
        }

        .livo-card-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);
            border-radius: 1.25rem;
            padding: 1.5rem;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 12px 25px -5px rgba(124, 58, 237, 0.4);
        }

        .livo-card-teal {
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            border-radius: 1.25rem;
            padding: 1.5rem;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 12px 25px -5px rgba(13, 148, 136, 0.4);
        }

        .livo-card-red {
            background: linear-gradient(135deg, rgba(127, 29, 29, 0.4) 0%, rgba(185, 28, 28, 0.25) 100%);
            border: 1px solid rgba(239, 68, 68, 0.6);
            border-radius: 1.25rem;
            padding: 1.5rem;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 12px 25px -5px rgba(220, 38, 38, 0.3);
        }

        .livo-card-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        .livo-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 0.85rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .livo-services-title {
            font-size: 0.8rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .livo-bottom-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .livo-service-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 1.25rem;
            padding: 1.25rem 1rem;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.3s ease;
            min-height: 120px;
        }

        .livo-service-card div:nth-child(2) {
            font-weight: 800;
            font-size: 0.95rem;
            margin-top: 0.35rem;
            margin-bottom: 0.15rem;
            line-height: 1.2;
        }

        .livo-service-card div:last-child {
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .livo-welcome-card {
            background: linear-gradient(135deg, #0c1626 0%, #15233b 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .livo-welcome-title { color: #ffffff !important; }
        .livo-welcome-subtitle { color: #94a3b8 !important; }
        .livo-sos-title { color: #f87171; }
        .livo-sos-desc { color: #fca5a5; }

        /* MODO CLARO ADAPTATIVO */
        html:not(.dark) .livo-welcome-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2) !important;
        }

        html:not(.dark) .livo-welcome-title { color: #0f172a !important; }
        html:not(.dark) .livo-welcome-subtitle { color: #475569 !important; }

        html:not(.dark) .livo-card-red {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
            border: 1px solid #fca5a5 !important;
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.2) !important;
        }

        html:not(.dark) .livo-sos-title { color: #991b1b !important; font-weight: 900 !important; }
        html:not(.dark) .livo-sos-desc { color: #7f1d1d !important; font-weight: 700 !important; }

        html:not(.dark) .livo-service-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-service-card div { color: #0f172a !important; }
        html:not(.dark) .livo-service-card div:last-child { color: #64748b !important; }

        /* BARRA DE NAVEGACIÓN INFERIOR MÓVIL ESTILO APP NATIVA */
        .livo-mobile-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 9999;
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

        @media (min-width: 768px) {
            .livo-mobile-navbar { display: none !important; }
            .livo-escritorio-container { padding-bottom: 0; }
        }
    </style>

    <div class="livo-escritorio-container" x-data="{ openSiriModal: false }" wire:poll.5s>
        @php
            $authUser = auth()->user();
            $dpto = $authUser?->departamento;
            $numeroDpto = $dpto?->numero ?? 'N/A';
            $condoNombre = $condominio?->nombre ?? $dpto?->condominio?->nombre ?? 'Sin Condominio';
$condoSlug = rawurlencode($condoNombre);
            $alertaActiva = \App\Models\AlertaSOS::where('departamento_id', $authUser->departamento_id)
                ->where('estado', 'Pendiente')
                ->first();

            $totalPaquetes = is_countable($paquetesPendientes) ? count($paquetesPendientes) : (int)$paquetesPendientes;

            // RUTAS NATIVAS OFICIALES DE FILAMENT
            $urlEscritorio = "/edificio/{$condoSlug}/escritorio";
            $urlPagos = \App\Filament\Vecino\Resources\PagoResource::getUrl('index');
            $urlInvitados = \App\Filament\Vecino\Resources\VisitaResource::getUrl('index');
            $urlReservas = \App\Filament\Vecino\Resources\ReservaResource::getUrl('index');
            $urlComunicados = \App\Filament\Vecino\Resources\ComunicadoResource::getUrl('index');
        @endphp

        {{-- 1. NOTIFICACIÓN FLOTANTE DE ENCOMIENDAS --}}
        @if($totalPaquetes > 0)
            <div class="livo-paquete-bar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: rgba(245, 158, 11, 0.2); padding: 0.75rem; border-radius: 0.85rem; color: #f59e0b;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 1.05rem; color: #ffffff;">📦 ¡TIENES {{ $totalPaquetes }} ENCOMIENDA(S) EN PORTERÍA!</div>
                        <div style="font-size: 0.85rem; color: #fbbf24;">Por favor acércate a la garita para recoger tu paquete.</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 2. TARJETA DE BIENVENIDA AL VECINO Y BOTÓN DE GUÍA SIRI --}}
        <div class="livo-welcome-card">
            <div>
                <h1 class="livo-welcome-title" style="font-size: 1.4rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>👋 ¡Bienvenido, {{ $authUser->name }}!</span>
                </h1>
                <p class="livo-welcome-subtitle" style="font-size: 0.95rem; margin-top: 0.25rem; font-weight: 600;">
                    <span style="color: #0284c7; font-weight: 800;">Departamento {{ $numeroDpto }}</span> — {{ $condoNombre }}
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <button type="button" @click="openSiriModal = true" style="padding: 0.6rem 1.25rem; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; font-weight: 800; font-size: 0.8rem; border-radius: 9999px; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35); display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span>📲 Instalar Atajo de Voz Siri (1 Clic)</span>
                </button>

                @if($alertaActiva)
                    <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #059669; padding: 0.5rem 1.25rem; border-radius: 9999px; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                        🚨 ALERTA S.O.S. ACTIVADA
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. BARRA DE SISMO EN VIVO --}}
        @if(!empty($sismoActivo) && $sismoActivo)
            <div class="livo-sismo-bar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: rgba(239, 68, 68, 0.2); padding: 0.75rem; border-radius: 0.85rem; color: #ef4444;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 1rem; color: #ffffff;">🚨 ALERTA DE SISMO EN CURSO</div>
                        <div style="font-size: 0.8rem; color: #fca5a5;">Confirma tu estado de salud para notificar a la Portería.</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <button wire:click="responderSismo('Estoy Bien')" type="button" class="livo-sismo-btn-bien">🟢 ESTOY BIEN</button>
                    <button wire:click="responderSismo('Necesito Ayuda')" type="button" class="livo-sismo-btn-ayuda">🔴 NECESITO AYUDA</button>
                </div>
            </div>
        @endif

        {{-- 4. TARJETAS PRINCIPALES GRADIENTES (3 TARJETAS) --}}
        <div class="livo-top-grid">
            <div class="livo-card-purple">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="livo-card-title" style="color: #c4b5fd;">ESTADO DE CUENTA</div>
                        <div style="font-size: 1.8rem; font-weight: 900; margin-top: 0.2rem;">
                            S/ {{ number_format($deudaTotal ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="livo-icon-box">
                        <svg style="width: 22px; height: 22px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div style="padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15); font-size: 0.75rem; font-weight: 600;">
                    @if(($deudaTotal ?? 0) > 0) ⚠️ Tienes recibos pendientes @else ✅ ¡Estás al día! @endif
                </div>
            </div>

            <div class="livo-card-teal">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="livo-card-title" style="color: #99f6e4;">ÚLTIMO COMUNICADO</div>
                        <div style="font-size: 1.1rem; font-weight: 800; margin-top: 0.2rem;">
                            {{ $ultimoAviso?->titulo ?? 'Sin comunicados' }}
                        </div>
                        <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.2rem;">
                            {{ Str::limit($ultimoAviso?->contenido ?? 'No hay novedades', 45) }}
                        </div>
                    </div>
                    <div class="livo-icon-box">
                        <svg style="width: 22px; height: 22px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 000-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                </div>
                <div style="padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <a href="/vecino/edificio/{{ $condoNombre }}/comunicados" style="color: #ffffff; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                        Ver todos los comunicados &rarr;
                    </a>
                </div>
            </div>

            @if($alertaActiva)
                <div class="livo-card-green-active">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="livo-card-title" style="color: #a7f3d0; font-weight: 800;">🚨 ALERTA S.O.S. ACTIVADA</div>
                            <div style="font-size: 0.8rem; color: #d1fae5; margin-top: 0.25rem; font-weight: 600;">
                                La Portería fue notificada y la ayuda está en camino.
                            </div>
                        </div>
                        <div class="livo-icon-box" style="background: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3);">
                            <svg style="width: 22px; height: 22px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <button disabled style="width: 100%; padding: 0.65rem 1rem; border-radius: 0.85rem; background: #047857; color: #ffffff; font-weight: 900; font-size: 0.8rem; border: 1px solid #34d399; cursor: default;">
                        🟢 AYUDA EN CAMINO
                    </button>
                </div>
            @else
                <div class="livo-card-red">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="livo-card-title livo-sos-title">🚨 BOTÓN DE PÁNICO S.O.S.</div>
                            <div class="livo-sos-desc" style="font-size: 0.8rem; margin-top: 0.25rem;">
                                Alerta instantánea a Portería en caso de emergencia real.
                            </div>
                        </div>
                        <div class="livo-icon-box" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4);">
                            <svg style="width: 22px; height: 22px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <button wire:click="dispararSOS" style="width: 100%; padding: 0.65rem 1rem; border-radius: 0.85rem; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff; font-weight: 900; font-size: 0.8rem; border: none; cursor: pointer; box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                        🚨 DISPARAR S.O.S. (1 TOQUE)
                    </button>
                </div>
            @endif
        </div>

       {{-- 5. SERVICIOS DEL CONDOMINIO --}}
<div class="livo-section-title">SERVICIOS DEL CONDOMINIO</div>
<div class="livo-bottom-grid">
    <a href="/edificio/{{ $condoSlug }}/pagos" class="livo-service-card">
        <div class="livo-service-icon-box" style="background: rgba(168, 85, 247, 0.2); border-color: #a855f7;">
            <svg style="width: 24px; height: 24px; color: #a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div class="livo-service-title">Mis Pagos</div>
        <div class="livo-service-desc">Recibos y vouchers</div>
    </a>

    <a href="/edificio/{{ $condoSlug }}/comunicados" class="livo-service-card">
        <div class="livo-service-icon-box" style="background: rgba(16, 185, 129, 0.2); border-color: #10b981;">
            <svg style="width: 24px; height: 24px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        </div>
        <div class="livo-service-title">Avisos</div>
        <div class="livo-service-desc">Comunicados</div>
    </a>

    <a href="/edificio/{{ $condoSlug }}/mascotas" class="livo-service-card">
        <div class="livo-service-icon-box" style="background: rgba(236, 72, 153, 0.2); border-color: #ec4899;">
            <svg style="width: 24px; height: 24px; color: #ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </div>
        <div class="livo-service-title">Mascotas</div>
        <div class="livo-service-desc">Registro</div>
    </a>

    <a href="/edificio/{{ $condoSlug }}/reclamos" class="livo-service-card">
        <div class="livo-service-icon-box" style="background: rgba(20, 184, 166, 0.2); border-color: #14b8a6;">
            <svg style="width: 24px; height: 24px; color: #14b8a6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <div class="livo-service-title">Reclamos</div>
        <div class="livo-service-desc">Sugerencias</div>
    </a>
</div>

        {{-- 6. BARRA DE NAVEGACIÓN INFERIOR MÓVIL ESTILO APP NATIVA --}}
        <div class="livo-mobile-navbar">
            <a href="{{ $urlEscritorio }}" class="livo-nav-item active">
                <span style="font-size: 1.25rem;">🏠</span>
                <span>Escritorio</span>
            </a>
            <a href="{{ $urlPagos }}" class="livo-nav-item">
                <span style="font-size: 1.25rem;">💰</span>
                <span>Finanzas</span>
            </a>
            <a href="{{ $urlInvitados }}" class="livo-nav-item">
                <span style="font-size: 1.25rem;">🛡️</span>
                <span>Seguridad</span>
            </a>
            <a href="{{ $urlReservas }}" class="livo-nav-item">
                <span style="font-size: 1.25rem;">⚙️</span>
                <span>Gestión</span>
            </a>
            <a href="{{ $urlComunicados }}" class="livo-nav-item">
                <span style="font-size: 1.25rem;">👥</span>
                <span>Comunidad</span>
            </a>
        </div>

        {{-- 7. MODAL EXPLICATIVO ATAJO SIRI --}}
        <div x-show="openSiriModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;" x-cloak>
            <div @click.away="openSiriModal = false" style="background: #0f172a; border: 1px solid #334155; border-radius: 1.5rem; max-width: 500px; width: 100%; padding: 2rem; color: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 2rem;">🎙️</span>
                        <div>
                            <h3 style="font-size: 1.2rem; font-weight: 900; color: #ffffff; margin: 0;">Atajo de Voz Siri</h3>
                            <span style="font-size: 0.75rem; color: #38bdf8; font-weight: 800; text-transform: uppercase;">Configuración en 1 Clic</span>
                        </div>
                    </div>
                    <button type="button" @click="openSiriModal = false" style="background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
                </div>

                <div style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6; space-y: 1rem;">
                    <p style="margin-bottom: 1rem;">
                        <strong>¿Para qué sirve?</strong><br>
                        Le permite pedir ayuda médica o de emergencia a la Portería desde cualquier lugar gritando a distancia: <strong style="color: #38bdf8;">"¡Oye Siri, Livo auxilio!"</strong>.
                    </p>

                    <div style="padding: 1rem; background: rgba(56, 189, 248, 0.1); border: 1px solid #0284c7; border-radius: 1rem; margin-bottom: 1.25rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #38bdf8; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">📌 TU CORREO REGISTRADO EN EL EDIFICIO:</span>
                        <div style="font-size: 1.1rem; font-weight: 900; color: #ffffff; letter-spacing: 0.02em;">
                            {{ $authUser->email }}
                        </div>
                        <span style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem; display: block;">
                            Al tocar el botón de abajo, Apple te hará 1 pregunta. Escribe o pega exactamente este correo.
                        </span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" @click="openSiriModal = false" style="padding: 0.65rem 1.25rem; background: #334155; color: #ffffff; font-weight: 700; font-size: 0.8rem; border-radius: 0.75rem; border: none; cursor: pointer;">
                        Cancelar
                    </button>
                    <a href="https://www.icloud.com/shortcuts/653d6f68abc0490a81e73c2773d36a90" target="_blank" @click="openSiriModal = false" style="padding: 0.65rem 1.25rem; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; font-weight: 800; font-size: 0.8rem; border-radius: 0.75rem; text-decoration: none; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);">
                        📲 Entendido, Abrir e Instalar Atajo
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>