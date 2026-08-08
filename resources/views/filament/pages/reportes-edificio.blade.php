<x-filament-panels::page>
    <style>
        .livo-report-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .livo-report-input {
            background: #1f2937;
            color: #ffffff;
            border: 1px solid #374151;
            border-radius: 0.75rem;
            padding: 0.6rem;
            width: 100%;
        }

        .livo-report-title {
            color: #ffffff !important;
        }

        .livo-report-text {
            color: #cbd5e1 !important;
        }

        .livo-report-row {
            border-bottom: 1px solid #1f2937;
        }

        /* MODO CLARO ADAPTATIVO */
        html:not(.dark) .livo-report-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-report-input {
            background: #f8fafc !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
        }

        html:not(.dark) .livo-report-title {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-report-text {
            color: #475569 !important;
        }

        html:not(.dark) .livo-report-row {
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
    </style>

    <div style="font-family: system-ui, -apple-system, sans-serif;" class="space-y-6">
        <!-- CABECERA EJECUTIVA -->
        @include('filament.components.header-card', [
            'icon' => '📊',
            'badge' => 'CONTABILIDAD & REPORTES',
            'title' => 'Centro de Reportes & Analíticas del Edificio',
            'description' => 'Generación de informes de morosidad, cobros, gastos y exportador directo a Microsoft Excel.',
            'actions' => null,
        ])

        <!-- BARRA DE FILTROS SEGMENTADOS -->
        <div class="livo-report-card">
            <h3 class="livo-report-title" style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem;">
                🔍 Filtros de Búsqueda y Segmentación
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.3rem;">Tipo de Reporte</label>
                    <select wire:model.live="tipo_reporte" class="livo-report-input" style="font-size: 0.85rem;">
                        <option value="morosidad">🔴 Reporte de Morosidad</option>
                        <option value="ingresos">🟢 Reporte de Ingresos / Cobros</option>
                        <option value="gastos">📉 Reporte de Gastos del Edificio</option>
                        <option value="visitas">👤 Reporte de Visitas e Ingresos</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.3rem;">Filtrar por Departamento</label>
                    <select wire:model.live="departamento_id" class="livo-report-input" style="font-size: 0.85rem;">
                        <option value="">Todos los Departamentos</option>
                        @foreach($departamentos as $dpto)
                            <option value="{{ $dpto->id }}">Dpto. {{ $dpto->numero }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.3rem;">Desde (Fecha)</label>
                    <input type="date" wire:model.live="fecha_inicio" class="livo-report-input" style="font-size: 0.85rem;">
                </div>

                <div>
                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.3rem;">Hasta (Fecha)</label>
                    <input type="date" wire:model.live="fecha_fin" class="livo-report-input" style="font-size: 0.85rem;">
                </div>

                <div>
                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.3rem;">Buscar DNI (Visitas)</label>
                    <input type="text" wire:model.live="buscar_dni" placeholder="Ej: 72839102" class="livo-report-input" style="font-size: 0.85rem;">
                </div>
            </div>
        </div>

        <!-- TABLA RESULTADO DE REPORTE FILTRADO CON BOTÓN EXCEL -->
        <div class="livo-report-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h3 class="livo-report-title" style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; margin: 0;">
                    RESULTADO DE LA CONSULTA ({{ count($resultados) }} REGISTROS)
                </h3>

                {{-- BOTÓN VERDE EXPORTAR A EXCEL (.CSV) --}}
                <button wire:click="exportarExcel" style="padding: 0.65rem 1.25rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 800; font-size: 0.8rem; border-radius: 0.75rem; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>EXPORTAR A EXCEL (.CSV)</span>
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;" class="livo-report-text">
                    <thead>
                        <tr class="livo-report-row" style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 0;">Detalle / Concepto</th>
                            <th style="padding: 0.75rem 0;">Referencia</th>
                            <th style="padding: 0.75rem 0;">Fecha</th>
                            <th style="padding: 0.75rem 0;">Estado / Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $item)
                            <tr class="livo-report-row">
                                <td class="livo-report-title" style="padding: 0.875rem 0; font-weight: 800;">
                                    {{ $item->concepto ?? $item->nombre_visitante ?? $item->titulo ?? 'Registro' }}
                                </td>
                                <td style="padding: 0.875rem 0;">
                                    {{ $item->departamento?->numero ? 'Dpto. ' . $item->departamento?->numero : ($item->dni_visitante ?? 'N/A') }}
                                </td>
                                <td style="padding: 0.875rem 0;">
                                    {{ $item->created_at ? $item->created_at->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td style="padding: 0.875rem 0; font-weight: 900;">
                                    @if(isset($item->monto))
                                        S/ {{ number_format($item->monto, 2) }}
                                    @else
                                        <span style="background: rgba(99, 102, 241, 0.2); color: #6366f1; padding: 0.2rem 0.5rem; border-radius: 0.4rem;">
                                            {{ $item->estado_visita ?? $item->estado ?? 'Registrado' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 1.5rem 0; text-align: center; color: #94a3b8;">
                                    No se encontraron registros con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>