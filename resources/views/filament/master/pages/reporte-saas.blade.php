<x-filament-panels::page>
    <style>
        .master-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
        }

        .master-stat-card {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .master-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .master-table th {
            text-align: left;
            padding: 0.85rem 1rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #9ca3af;
            border-bottom: 1px solid #374151;
            background: #1f2937;
        }

        .master-table td {
            padding: 0.85rem 1rem;
            font-size: 0.85rem;
            border-bottom: 1px solid #1f2937;
            color: #e5e7eb;
        }
    </style>

    <div class="space-y-6">
        
        {{-- 📊 2 TARJETAS DE INDICADORES CLAVE MRR & TOTAL --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
            
            {{-- TARJETA 1: MRR PROYECTADO --}}
            <div class="master-stat-card">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">
                        INGRESO MENSUAL RECURRENTE (MRR)
                    </div>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #38bdf8; margin-top: 0.35rem;">
                        S/ {{ number_format($mrrSaaS, 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.25rem; font-weight: 600;">
                        📈 Facturación recurrente por licencias activas
                    </div>
                </div>
                <div style="padding: 1rem; background: rgba(56, 189, 248, 0.15); border-radius: 1rem; color: #38bdf8;">
                    <svg style="width: 36px; height: 36px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            {{-- TARJETA 2: VOLUMEN TOTAL PROCESADO --}}
            <div class="master-stat-card">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">
                        VOLUMEN TOTAL DE MANTENIMIENTO PROCESADO
                    </div>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #a855f7; margin-top: 0.35rem;">
                        S/ {{ number_format($totalProcesado, 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #c084fc; margin-top: 0.25rem; font-weight: 600;">
                        💼 Recaudación procesada en la plataforma
                    </div>
                </div>
                <div style="padding: 1rem; background: rgba(168, 85, 247, 0.15); border-radius: 1rem; color: #c084fc;">
                    <svg style="width: 36px; height: 36px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>

        </div>

        {{-- 🏢 TABLA COMPARATIVA DE EDIFICIOS / CONDOMINIOS --}}
        <div class="master-card">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin-bottom: 0.25rem;">
                🏢 Desglose de Condominios Suscritos
            </h3>
            <p style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 1rem;">
                Métricas de plan, departamentos y vigencia de suscripción por edificio.
            </p>

            <div style="overflow-x: auto;">
                <table class="master-table">
                    <thead>
                        <tr>
                            <th>Condominio</th>
                            <th>Plan SaaS</th>
                            <th>Departamentos</th>
                            <th>Tarifa Mensual</th>
                            <th>Estado</th>
                            <th>Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($condominios as $c)
                            <tr>
                                <td style="font-weight: 800; color: #ffffff;">{{ $c->nombre }}</td>
                                <td>
                                    <span style="padding: 0.25rem 0.65rem; background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">
                                        {{ $c->plan_saas ?? 'Básico' }}
                                    </span>
                                </td>
                                <td>{{ $c->departamentos_count }} dptos</td>
                                <td style="font-weight: 700; color: #34d399;">S/ {{ number_format($c->precio_mensual_saas ?? 0, 2) }}</td>
                                <td>
                                    @if($c->estado_servicio === 'Activo')
                                        <span style="padding: 0.25rem 0.65rem; background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">🟢 Activo</span>
                                    @else
                                        <span style="padding: 0.25rem 0.65rem; background: rgba(239, 68, 68, 0.15); color: #f87171; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">🔴 Suspendido</span>
                                    @endif
                                </td>
                                <td style="color: #9ca3af;">
                                    {{ $c->fecha_vencimiento_saas ? date('d/m/Y', strtotime($c->fecha_vencimiento_saas)) : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #6b7280; padding: 2rem;">
                                    No hay condominios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>