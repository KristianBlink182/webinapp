<x-filament-panels::page>
    <style>
        .livo-sub-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
        }

        .livo-sub-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 900px) {
            .livo-sub-stat-grid {
                grid-template-columns: 1fr;
            }
        }

        .livo-sub-stat {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .livo-bank-box {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 1.25rem;
        }

        /* ESTILOS ADAPTATIVOS MODO CLARO */
        html:not(.dark) .livo-sub-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2) !important;
        }

        html:not(.dark) .livo-sub-stat {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-bank-box {
            background: #f8fafc !important;
            border: 1px solid rgba(148, 163, 184, 0.25) !important;
            color: #0f172a !important;
        }

        html:not(.dark) .livo-table-box {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-table-row {
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        }

        html:not(.dark) .livo-text-main {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-text-muted {
            color: #475569 !important;
        }
    </style>

    <div class="space-y-6">
        @php
            $tenant = \Filament\Facades\Filament::getTenant();
            $plan = $tenant?->plan_saas ?? 'Básico';
            $precio = $tenant?->precio_mensual_saas ?? 100;
            $estado = $tenant?->estado_servicio ?? 'Activo';
            $vencimiento = $tenant?->fecha_vencimiento_saas;
            $estadoPago = $tenant?->estado_pago_saas ?? 'Al Día';
            $facturaUrl = $tenant?->comprobante_factura_saas ? asset('storage/' . $tenant->comprobante_factura_saas) : null;

            $diasRestantes = $vencimiento ? round((strtotime($vencimiento) - time()) / (60 * 60 * 24)) : 'N/A';

            // LECTURA DINÁMICA DE CUENTAS BANCARIAS CONFIGURADAS DESDE EL MASTER
            $saasConfigPath = storage_path('app/saas_config.json');
            $saasConfig = file_exists($saasConfigPath) ? json_decode(file_get_contents($saasConfigPath), true) : [
                'bcp_soles' => '191-98765432-0-12',
                'cci_bcp' => '002-191-0098765432012-54',
                'bbva_soles' => '0011-0123-0100098765-88',
                'ruc' => '20601234567',
                'razon_social' => 'PROYECTOS LIVO S.A.C.',
                'yape_numero' => '987 654 321',
                'yape_titular' => 'LIVO SaaS Oficial',
            ];
        @endphp

        {{-- 1. INDICADORES DE VENCIMIENTO Y PLAN --}}
        <div class="livo-sub-stat-grid">
            <div class="livo-sub-stat">
                <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">PLAN CONTRATADO</div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #0284c7; margin-top: 0.25rem;">
                    Plan {{ $plan }}
                </div>
                <div class="livo-text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
                    Tarifa Base: <strong>S/ {{ number_format($precio, 2) }} / mes</strong>
                </div>
            </div>

            <div class="livo-sub-stat">
                <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">ESTADO DE SUSCRIPCIÓN</div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #059669; margin-top: 0.25rem;">
                    @if($estado === 'Activo')
                        🟢 {{ $estado }}
                    @else
                        🔴 {{ $estado }}
                    @endif
                </div>
                <div style="font-size: 0.75rem; color: #d97706; margin-top: 0.25rem; font-weight: 700;">
                    Estado de Cobro: {{ $estadoPago }}
                </div>
            </div>

            <div class="livo-sub-stat">
                <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">PRÓXIMO VENCIMIENTO</div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #7c3aed; margin-top: 0.25rem;">
                    {{ $vencimiento ? date('d/m/Y', strtotime($vencimiento)) : 'Sin fecha' }}
                </div>
                <div class="livo-text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
                    Quedan: <strong>{{ $diasRestantes }} días de servicio</strong>
                </div>
            </div>
        </div>

        {{-- 2. DATOS BANCARIOS Y FORMULARIO DE PAGO --}}
        <div class="livo-sub-card">
            <h3 class="livo-text-main" style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">
                💳 Métodos de Pago Oficiales de LIVO SaaS
            </h3>
            <p class="livo-text-muted" style="font-size: 0.8rem; margin-bottom: 1.25rem;">
                Realiza tu abono mensual utilizando cualquiera de nuestras cuentas bancarias o billeteras digitales:
            </p>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                {{-- BANCOS --}}
                <div class="livo-bank-box">
                    <h4 style="font-weight: 800; color: #0284c7; font-size: 0.95rem; margin-bottom: 0.75rem;">🏦 Cuentas Bancarias LIVO S.A.C.</h4>
                    <ul class="livo-text-main" style="font-size: 0.85rem; line-height: 1.8; list-style: none; padding: 0; margin: 0;">
                        <li><strong>BCP Soles:</strong> {{ $saasConfig['bcp_soles'] ?? '191-98765432-0-12' }}</li>
                        <li><strong>CCI BCP:</strong> {{ $saasConfig['cci_bcp'] ?? '002-191-0098765432012-54' }}</li>
                        <li><strong>BBVA Soles:</strong> {{ $saasConfig['bbva_soles'] ?? '0011-0123-0100098765-88' }}</li>
                        <li><strong>RUC Empresa:</strong> {{ $saasConfig['ruc'] ?? '20601234567' }}</li>
                        <li><strong>Razón Social:</strong> {{ $saasConfig['razon_social'] ?? 'PROYECTOS LIVO S.A.C.' }}</li>
                    </ul>
                </div>

                {{-- YAPE / PLIN --}}
                <div class="livo-bank-box" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="font-weight: 800; color: #7c3aed; font-size: 0.95rem; margin-bottom: 0.5rem;">📱 Yape / Plin LIVO</h4>
                        <p class="livo-text-main" style="font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Número:</strong> {{ $saasConfig['yape_numero'] ?? '987 654 321' }}</p>
                        <p class="livo-text-main" style="font-size: 0.85rem; margin: 0;"><strong>Titular:</strong> {{ $saasConfig['yape_titular'] ?? 'LIVO SaaS Oficial' }}</p>
                    </div>
                    <div style="padding: 0.5rem; background: #ffffff; border-radius: 0.85rem; border: 1px solid #cbd5e1;">
                        <img src="{{ asset('images/logo.png') }}" alt="QR Yape" style="width: 80px; height: 80px; object-fit: contain;">
                    </div>
                </div>
            </div>

            {{-- FORMULARIO PARA SUBIR VOUCHER --}}
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                <form wire:submit.prevent="registrarPago" class="space-y-4">
                    {{ $this->form }}

                    <button type="submit" style="padding: 0.85rem 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 800; font-size: 0.9rem; border-radius: 0.85rem; border: none; cursor: pointer; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                        💾 REGISTRAR PAGO Y SUBIR VOUCHER SAAS
                    </button>
                </form>
            </div>
        </div>

        {{-- 3. HISTORIAL DE PAGOS SAAS (PAGINADO DE 5 EN 5 CON FLECHITAS) --}}
        <div class="livo-sub-card livo-table-box">
            <h3 class="livo-text-main" style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                <span>📋 Historial de Pagos de Suscripción LIVO SaaS</span>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Paginado a 5 registros</span>
            </h3>

            @if(!empty($historialPagos) && count($historialPagos) > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr class="livo-table-row" style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.75rem 0.5rem;">Fecha y Hora</th>
                                <th style="padding: 0.75rem 0.5rem;">Tipo / Datos Fiscales</th>
                                <th style="padding: 0.75rem 0.5rem;">Monto Cobrado</th>
                                <th style="padding: 0.75rem 0.5rem;">Estado</th>
                                <th style="padding: 0.75rem 0.5rem; text-align: right;">Comprobantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historialPagos as $pago)
                                <tr class="livo-table-row" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 0.85rem 0.5rem; font-weight: 700;">
                                        {{ $pago->created_at->format('d/m/Y - h:i A') }}
                                    </td>
                                    <td style="padding: 0.85rem 0.5rem;">
                                        @if($pago->tipo_comprobante === 'Factura')
                                            <span style="color: #0284c7; font-weight: 800;">🏢 FACTURA (+18% IGV)</span>
                                            <div style="font-size: 0.75rem; opacity: 0.8;">
                                                RUC: {{ $pago->ruc ?? 'Sin RUC' }} — {{ $pago->razon_social ?? '' }}
                                            </div>
                                        @else
                                            <span style="color: #7c3aed; font-weight: 800;">👤 BOLETA DE VENTA</span>
                                            <div style="font-size: 0.75rem; opacity: 0.8;">
                                                DNI: {{ $pago->dni ?? 'Sin DNI' }} — {{ $pago->nombre ?? '' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.85rem 0.5rem;">
                                        <div style="font-size: 1rem; font-weight: 900; color: #059669;">
                                            S/ {{ number_format($pago->monto_total ?? $pago->monto, 2) }}
                                        </div>
                                        @if($pago->monto_igv > 0)
                                            <div style="font-size: 0.7rem; color: #64748b;">
                                                (Base: S/ {{ number_format($pago->monto_base, 2) }} + IGV: S/ {{ number_format($pago->monto_igv, 2) }})
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.85rem 0.5rem;">
                                        @if($pago->estado === 'Pago por Verificar')
                                            <span style="background: rgba(217, 119, 6, 0.15); border: 1px solid #d97706; color: #d97706; padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">
                                                🟡 En Revisión por Master
                                            </span>
                                        @else
                                            <span style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #059669; padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">
                                                🟢 Aprobado & Verificado
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.85rem 0.5rem; text-align: right;">
                                        @if($pago->voucher)
                                            <a href="{{ asset('storage/' . $pago->voucher) }}" target="_blank" style="padding: 0.4rem 0.85rem; background: rgba(2, 132, 199, 0.15); border: 1px solid #0284c7; color: #0284c7; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; text-decoration: none; display: inline-block; margin-left: 0.25rem;">
                                                📷 Voucher
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- PAGINADOR CON FLECHITAS --}}
                <div style="margin-top: 1rem;">
                    {{ $historialPagos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>