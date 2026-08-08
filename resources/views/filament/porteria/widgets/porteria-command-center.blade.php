<x-filament-widgets::widget>
    <style>
        .porteria-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
            transition: all 0.3s ease;
        }

        .porteria-btn-sismo-act {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #ffffff;
            font-weight: 800;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 1px solid #ef4444;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .porteria-btn-sismo-fin {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            font-weight: 800;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 1px solid #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .porteria-grid-6 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 900px) { .porteria-grid-6 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 550px) { .porteria-grid-6 { grid-template-columns: 1fr; } }

        .porteria-op-btn {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 1rem;
            padding: 1.25rem 1rem;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .porteria-op-btn:hover {
            border-color: #38bdf8;
            background: #1e293b;
            transform: translateY(-2px);
        }

        .porteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .porteria-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #9ca3af;
            border-bottom: 1px solid #374151;
            background: #1f2937;
        }

        .porteria-table td {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            border-bottom: 1px solid #1f2937;
            color: #e5e7eb;
        }

        /* ESTILOS ADAPTATIVOS MODO CLARO PORTERÍA */
        html:not(.dark) .porteria-card,
        html:not(.dark) .porteria-welcome-box {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .porteria-title-text { color: #0f172a !important; }
        html:not(.dark) .porteria-muted-text { color: #475569 !important; }

        html:not(.dark) .porteria-op-btn {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 4px 12px -2px rgba(148, 163, 184, 0.12) !important;
        }

        html:not(.dark) .porteria-op-btn:hover {
            background: #f8fafc !important;
            border-color: #0284c7 !important;
        }

        html:not(.dark) .porteria-table th {
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        html:not(.dark) .porteria-table td {
            color: #0f172a !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
    </style>

    <div class="space-y-6" wire:poll.3s x-data="{ openSalvoModal: false }">
        @php
            $tenant = \Filament\Facades\Filament::getTenant();
            $nombrePortero = auth()->user()->name ?? 'Personal de Vigilancia';
            $visitasConteo = $visitasCount ?? $visitasDentroCount ?? $visitasDentro ?? 0;
            $paquetesConteo = is_countable($paquetesPendientes ?? null) ? count($paquetesPendientes) : ($paquetesPendientes ?? 0);
            $aSalvoConteo = is_countable($confirmadosASalvo ?? null) ? count($confirmadosASalvo) : (int)($confirmadosASalvo ?? 0);
        @endphp

        {{-- 1. SALUDO PERSONALIZADO PARA EL PORTERO --}}
        <div class="porteria-welcome-box" style="border-radius: 1.25rem; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 54px; height: 54px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                    👮‍♂️
                </div>
                <div>
                    <h1 class="porteria-title-text" style="font-size: 1.6rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>Bienvenido, {{ $nombrePortero }} 👋</span>
                    </h1>
                    <p class="porteria-muted-text" style="font-size: 0.85rem; margin-top: 0.25rem; font-weight: 600;">
                        Terminal de Seguridad y Control de Accesos de <span style="color: #0284c7; font-weight: 800;">{{ $tenant?->nombre ?? 'Condominio' }}</span>.
                    </p>
                </div>
            </div>

            <div style="padding: 0.5rem 1.15rem; background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #059669; font-weight: 800; font-size: 0.8rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                TURNO EN VIVO ACTIVO
            </div>
        </div>

        <div class="porteria-card">
            {{-- 2. EMERGENCIAS ACTIVAS (SOLO SOLICITUDES PENDIENTES) --}}
            @if(isset($alertasSOS) && (($alertasSOS->where('estado', 'Pendiente')->count() ?? 0) > 0 || (isset($auxiliosSismo) && $auxiliosSismo->count() > 0)))
                <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 2px solid #ef4444; border-radius: 1rem; box-shadow: 0 0 25px rgba(239, 68, 68, 0.3);">
                    <h3 style="font-size: 1.1rem; font-weight: 900; color: #991b1b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🚨 SOLICITUDES DE EMERGENCIA ACTIVAS</span>
                    </h3>

                    @if(isset($alertasSOS))
                        @foreach($alertasSOS->where('estado', 'Pendiente') as $sos)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: #ffffff; border: 1px solid #fca5a5; border-radius: 0.75rem; margin-bottom: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                <div>
                                    <span style="font-weight: 900; color: #991b1b; font-size: 0.95rem;">🚨 S.O.S. - Dpto. {{ $sos->departamento?->numero ?? 'N/A' }}</span>
                                    <span style="font-size: 0.85rem; color: #7f1d1d; font-weight: 800; margin-left: 0.5rem;">({{ $sos->user?->name ?? 'Residente' }})</span>
                                </div>
                                <button wire:click="atenderSOS({{ $sos->id }})" type="button" style="padding: 0.45rem 1rem; background: #10b981; color: #ffffff; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                    ✓ Marcar Atendido
                                </button>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($auxiliosSismo))
                        @foreach($auxiliosSismo as $auxilio)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: #ffffff; border: 1px solid #fca5a5; border-radius: 0.75rem; margin-bottom: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                <div>
                                    <span style="font-weight: 900; color: #dc2626; font-size: 0.95rem;">🚨 AUXILIO SISMO - Dpto. {{ $auxilio->departamento?->numero ?? 'N/A' }}</span>
                                    <span style="font-size: 0.85rem; color: #7f1d1d; font-weight: 800; margin-left: 0.5rem;">({{ $auxilio->user?->name }})</span>
                                </div>
                                <button wire:click="atenderAuxilioSismo({{ $auxilio->id }})" type="button" style="padding: 0.45rem 1rem; background: #10b981; color: #ffffff; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                    ✓ Marcar Atendido
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            {{-- 3. FILA SUPERIOR: 3 TARJETAS RESUMEN --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;">
                <div class="porteria-op-btn" style="justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">VISITAS EN EL EDIFICIO</div>
                        <div class="porteria-title-text" style="font-size: 2rem; font-weight: 800; color: #38bdf8;">{{ $visitasConteo }}</div>
                    </div>
                    <div style="padding: 0.75rem; background: rgba(56, 189, 248, 0.15); border-radius: 0.75rem; color: #38bdf8;">
                        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <div class="porteria-op-btn" style="justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">PAQUETES EN RECEPCIÓN</div>
                        <div class="porteria-title-text" style="font-size: 2rem; font-weight: 800; color: #f59e0b;">{{ $paquetesConteo }}</div>
                    </div>
                    <div style="padding: 0.75rem; background: rgba(245, 158, 11, 0.15); border-radius: 0.75rem; color: #f59e0b;">
                        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: center;">
                    @if(!empty($sismoActivo) && $sismoActivo)
                        <button wire:click="finalizarAlertaSismo" class="porteria-btn-sismo-fin">🟢 FINALIZAR ALERTA SISMO</button>
                    @else
                        <button wire:click="activarAlertaSismo" class="porteria-btn-sismo-act">🚨 ACTIVAR ALERTA SISMO</button>
                    @endif
                </div>
            </div>

            {{-- 4. BOTONERA OPERATIVA DE 6 ACCIONES --}}
            <div class="porteria-grid-6">
                <a href="{{ \App\Filament\Porteria\Resources\VisitaResource::getUrl('index') }}" class="porteria-op-btn">
                    <div style="padding: 0.75rem; background: rgba(56, 189, 248, 0.15); color: #0284c7; border-radius: 0.75rem;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Registrar Visita</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Ingresos y salidas</div>
                    </div>
                </a>

                <a href="{{ \App\Filament\Porteria\Resources\PaqueteResource::getUrl('index') }}" class="porteria-op-btn">
                    <div style="padding: 0.75rem; background: rgba(245, 158, 11, 0.15); color: #d97706; border-radius: 0.75rem;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Recibir Paquete</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Encomiendas</div>
                    </div>
                </a>

                <div class="porteria-op-btn">
                    <div style="padding: 0.75rem; background: rgba(239, 68, 68, 0.15); color: #dc2626; border-radius: 0.75rem;">
                        <span style="font-size: 1.2rem; font-weight: 900;">🚨</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Alertas S.O.S. ({{ isset($alertasSOS) ? $alertasSOS->where('estado', 'Pendiente')->count() : 0 }})</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Emergencias activas</div>
                    </div>
                </div>

                <div class="porteria-op-btn">
                    <div style="padding: 0.75rem; background: rgba(239, 68, 68, 0.15); color: #dc2626; border-radius: 0.75rem;">
                        <span style="font-size: 1.2rem; font-weight: 900;">🔴</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Sismo Auxilio ({{ isset($auxiliosSismo) ? $auxiliosSismo->count() : 0 }})</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Vecinos que piden ayuda</div>
                    </div>
                </div>

                {{-- TARJETA SISMO A SALVO (AL HACER CLIC ABRE EL MODAL DE AUDITORÍA) --}}
                <div class="porteria-op-btn" @click="openSalvoModal = true">
                    <div style="padding: 0.75rem; background: rgba(16, 185, 129, 0.15); color: #059669; border-radius: 0.75rem;">
                        <span style="font-size: 1.2rem; font-weight: 900;">🟢</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Sismo A Salvo ({{ $aSalvoConteo }})</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Clic para ver registro</div>
                    </div>
                </div>

                <a href="{{ \App\Filament\Porteria\Resources\ReservaResource::getUrl('index') }}" class="porteria-op-btn">
                    <div style="padding: 0.75rem; background: rgba(168, 85, 247, 0.15); color: #7c3aed; border-radius: 0.75rem;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem;">Áreas Comunes</div>
                        <div class="porteria-muted-text" style="font-size: 0.75rem;">Calendario de uso</div>
                    </div>
                </a>
            </div>

            {{-- 5. HISTORIAL Y MEDIDOR DE TIEMPOS DE RESPUESTA S.O.S. --}}
            <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <h3 class="porteria-title-text" style="font-size: 0.95rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📋 Historial de Emergencias S.O.S & Tiempos de Respuesta</span>
                    </h3>
                    <span class="porteria-muted-text" style="font-size: 0.75rem;">Auditoría de atención en Portería</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="porteria-table">
                        <thead>
                            <tr>
                                <th>Dpto / Vecino</th>
                                <th>Tipo Alerta</th>
                                <th>Hora Solicitada</th>
                                <th>Hora Atendida</th>
                                <th>Tiempo Respuesta</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($historialSOS))
                                @forelse($historialSOS as $sos)
                                    @php
                                        $emision = \Carbon\Carbon::parse($sos->created_at)->timezone('America/Lima');
                                        
                                        $rawAtencion = $sos->fecha_atendido ?? ($sos->estado === 'Atendido' ? $sos->updated_at : null);
                                        $atencion = $rawAtencion ? \Carbon\Carbon::parse($rawAtencion)->timezone('America/Lima') : null;
                                        
                                        $tiempoRespuesta = $atencion ? $emision->diffForHumans($atencion, true) : 'En Espera';
                                    @endphp
                                    <tr>
                                        <td class="porteria-title-text" style="font-weight: 800;">
                                            Dpto. {{ $sos->departamento?->numero ?? 'N/A' }}
                                            <div class="porteria-muted-text" style="font-weight: 400; font-size: 0.75rem;">{{ $sos->user?->name ?? 'Residente' }}</div>
                                        </td>
                                        <td>
                                            <span style="padding: 0.2rem 0.55rem; background: rgba(239, 68, 68, 0.15); color: #dc2626; border-radius: 9999px; font-weight: 700; font-size: 0.7rem;">
                                                🚨 {{ $sos->tipo ?? 'Emergencia' }}
                                            </span>
                                        </td>
                                        <td>{{ $emision->format('d/m/Y h:i A') }}</td>
                                        <td>{{ $atencion ? $atencion->format('d/m/Y h:i A') : '—' }}</td>
                                        <td>
                                            @if($atencion)
                                                <span style="padding: 0.2rem 0.55rem; background: rgba(16, 185, 129, 0.15); color: #059669; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">
                                                    ⏱️ {{ $tiempoRespuesta }}
                                                </span>
                                            @else
                                                <span style="padding: 0.2rem 0.55rem; background: rgba(239, 68, 68, 0.15); color: #dc2626; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">
                                                    ⏳ En Espera
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sos->estado === 'Atendido')
                                                <span style="color: #059669; font-weight: 800;">🟢 Atendido</span>
                                            @else
                                                <span style="color: #dc2626; font-weight: 800;">🔴 Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="porteria-muted-text" style="padding: 1.5rem; text-align: center;">
                                            No hay emergencias registradas en el historial.
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 6. MODAL AUDITORÍA DE VECINOS CONFIRMADOS A SALVO EN SISMO --}}
        <div x-show="openSalvoModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;" x-cloak>
            <div @click.away="openSalvoModal = false" style="background: #0f172a; border: 1px solid #334155; border-radius: 1.5rem; max-width: 600px; width: 100%; padding: 2rem; color: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.8rem;">🟢</span>
                        <div>
                            <h3 style="font-size: 1.2rem; font-weight: 900; color: #ffffff; margin: 0;">Padrón de Vecinos a Salvo (Sismo)</h3>
                            <span style="font-size: 0.75rem; color: #34d399; font-weight: 800; text-transform: uppercase;">Total: {{ $aSalvoConteo }} Confirmados</span>
                        </div>
                    </div>
                    <button type="button" @click="openSalvoModal = false" style="background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
                </div>

                <div style="max-height: 350px; overflow-y: auto;">
                    @if(isset($confirmadosASalvo) && count($confirmadosASalvo) > 0)
                        <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.85rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid #334155; color: #94a3b8; font-size: 0.75rem; text-transform: uppercase;">
                                    <th style="padding: 0.6rem;">Dpto</th>
                                    <th style="padding: 0.6rem;">Vecino</th>
                                    <th style="padding: 0.6rem;">Hora Confirmada</th>
                                    <th style="padding: 0.6rem; text-align: right;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($confirmadosASalvo as $salvo)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 0.75rem 0.6rem; font-weight: 800; color: #38bdf8;">Dpto. {{ $salvo->departamento?->numero ?? 'N/A' }}</td>
                                        <td style="padding: 0.75rem 0.6rem; font-weight: 700; color: #ffffff;">{{ $salvo->user?->name ?? 'Residente' }}</td>
                                        <td style="padding: 0.75rem 0.6rem; color: #cbd5e1;">{{ \Carbon\Carbon::parse($salvo->updated_at)->timezone('America/Lima')->format('d/m/Y - h:i A') }}</td>
                                        <td style="padding: 0.75rem 0.6rem; text-align: right; color: #34d399; font-weight: 800;">🟢 A salvo</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="text-align: center; color: #94a3b8; padding: 2rem 0; font-style: italic;">
                            No hay vecinos confirmados a salvo todavía.
                        </p>
                    @endif
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" @click="openSalvoModal = false" style="padding: 0.65rem 1.25rem; background: #334155; color: #ffffff; font-weight: 800; font-size: 0.8rem; border-radius: 0.75rem; border: none; cursor: pointer;">
                        Cerrar Ventana
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>