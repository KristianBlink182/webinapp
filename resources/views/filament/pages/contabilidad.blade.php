<x-filament-panels::page>
    <style>
        .livo-conta-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .livo-conta-table-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .livo-conta-title {
            color: #ffffff !important;
        }

        .livo-conta-text {
            color: #cbd5e1 !important;
        }

        .livo-conta-row {
            border-bottom: 1px solid #1f2937;
        }

        /* MODO CLARO ADAPTATIVO */
        html:not(.dark) .livo-conta-card,
        html:not(.dark) .livo-conta-table-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-conta-title {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-conta-text {
            color: #475569 !important;
        }

        html:not(.dark) .livo-conta-row {
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
    </style>

    <div style="font-family: system-ui, -apple-system, sans-serif;" class="space-y-6">
        <!-- CABECERA EJECUTIVA -->
        @include('filament.components.header-card', [
            'icon' => '📖',
            'badge' => 'CONTABILIDAD Y AUDITORÍA',
            'title' => 'Contabilidad de Caja & Libro Mayor',
            'description' => 'Historial financiero automatizado en tiempo real de todos los ingresos cobrados y egresos pagados.',
            'actions' => null,
        ])

        <!-- RESUMEN CONTABLE (3 TARJETAS) -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;">
            <div class="livo-conta-card">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">(+) TOTAL INGRESOS POR CUOTAS</span>
                <h2 style="font-size: 2rem; font-weight: 900; color: #34d399; margin-top: 0.25rem;">S/ {{ number_format($totalIngresos, 2) }}</h2>
            </div>

            <div class="livo-conta-card">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">(-) TOTAL EGRESOS Y FACTURAS</span>
                <h2 style="font-size: 2rem; font-weight: 900; color: #f87171; margin-top: 0.25rem;">S/ {{ number_format($totalEgresos, 2) }}</h2>
            </div>

            <div class="livo-conta-card">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">(=) SALDO REAL EN CAJA</span>
                <h2 style="font-size: 2rem; font-weight: 900; color: {{ $saldoCaja >= 0 ? '#38bdf8' : '#f87171' }}; margin-top: 0.25rem;">S/ {{ number_format($saldoCaja, 2) }}</h2>
            </div>
        </div>

        <!-- LIBRO MAYOR: ASIENTOS DE INGRESO Y EGRESO -->
        <div class="livo-conta-table-card">
            <h3 class="livo-conta-title" style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem;">
                📖 Libro Mayor de Movimientos de Caja
            </h3>

            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;" class="livo-conta-text">
                <thead>
                    <tr class="livo-conta-row" style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 0;">Fecha</th>
                        <th style="padding: 0.75rem 0;">Tipo Movimiento</th>
                        <th style="padding: 0.75rem 0;">Descripción / Concepto</th>
                        <th style="padding: 0.75rem 0;">Monto (S/)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingresos as $ing)
                        <tr class="livo-conta-row">
                            <td style="padding: 0.875rem 0;">{{ $ing->created_at->format('d/m/Y') }}</td>
                            <td style="padding: 0.875rem 0;"><span style="background: rgba(16, 185, 129, 0.2); color: #059669; padding: 0.2rem 0.5rem; border-radius: 0.4rem; font-weight: 800;">INGRESO</span></td>
                            <td class="livo-conta-title" style="padding: 0.875rem 0; font-weight: 700;">Cobro Dpto. {{ $ing->departamento?->numero }} - {{ $ing->concepto }}</td>
                            <td style="padding: 0.875rem 0; font-weight: 900; color: #059669;">+ S/ {{ number_format($ing->monto, 2) }}</td>
                        </tr>
                    @endforeach

                    @foreach($egresos as $egr)
                        <tr class="livo-conta-row">
                            <td style="padding: 0.875rem 0;">{{ $egr->created_at->format('d/m/Y') }}</td>
                            <td style="padding: 0.875rem 0;"><span style="background: rgba(239, 68, 68, 0.2); color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 0.4rem; font-weight: 800;">EGRESO</span></td>
                            <td class="livo-conta-title" style="padding: 0.875rem 0; font-weight: 700;">Gasto: {{ $egr->concepto }} [{{ $egr->numero_factura ?? 'S/N' }}]</td>
                            <td style="padding: 0.875rem 0; font-weight: 900; color: #dc2626;">- S/ {{ number_format($egr->monto, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>