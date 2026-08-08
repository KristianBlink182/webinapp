<x-filament-panels::page>
    <style>
        .master-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
            margin-bottom: 2rem;
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

    {{-- 1. TABLA DE CONTROL DE COBROS SAAS --}}
    <div class="master-card">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin-bottom: 0.25rem;">
            💰 Control de Pagos de Suscripciones SaaS LIVO
        </h3>
        <p style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 1.25rem;">
            Revisa los comprobantes enviados por los administradores y renueva las licencias mensuales en 1 solo clic.
        </p>

        <div style="overflow-x: auto;">
            <table class="master-table">
                <thead>
                    <tr>
                        <th>Condominio</th>
                        <th>Plan SaaS</th>
                        <th>Tarifa Mensual</th>
                        <th>Voucher Admin</th>
                        <th>Estado Cobro</th>
                        <th>Próximo Vencimiento</th>
                        <th style="text-align: right;">Acciones</th>
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
                            <td style="font-weight: 700; color: #34d399;">S/ {{ number_format($c->precio_mensual_saas ?? 0, 2) }}</td>
                            <td>
                                @if(!empty($c->voucher_saas))
                                    <a href="{{ asset('storage/' . $c->voucher_saas) }}" target="_blank" style="padding: 0.35rem 0.75rem; background: #0284c7; color: #ffffff; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; text-decoration: none;">
                                        📷 Ver Voucher
                                    </a>
                                @else
                                    <span style="color: #6b7280; font-size: 0.75rem;">Sin voucher</span>
                                @endif
                            </td>
                            <td>
                                @if($c->estado_pago_saas === 'Pago por Verificar')
                                    <span style="padding: 0.25rem 0.65rem; background: rgba(245, 158, 11, 0.2); color: #fbbf24; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">🟡 Pago por Verificar</span>
                                @elseif($c->estado_pago_saas === 'Al Día')
                                    <span style="padding: 0.25rem 0.65rem; background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">🟢 Al Día</span>
                                @else
                                    <span style="padding: 0.25rem 0.65rem; background: rgba(239, 68, 68, 0.15); color: #f87171; font-weight: 800; font-size: 0.75rem; border-radius: 9999px;">🔴 Pendiente</span>
                                @endif
                            </td>
                            <td style="color: #9ca3af; font-weight: 700;">
                                {{ $c->fecha_vencimiento_saas ? date('d/m/Y', strtotime($c->fecha_vencimiento_saas)) : 'N/A' }}
                            </td>
                            <td style="text-align: right;">
                                @if($c->estado_pago_saas === 'Pago por Verificar')
                                    <button wire:click="aprobarPago({{ $c->id }})" type="button" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);">
                                        🟡 APROBAR Y RENOVAR +1 MES
                                    </button>
                                @else
                                    <button disabled type="button" style="padding: 0.5rem 1rem; background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 800; font-size: 0.75rem; border-radius: 0.5rem; cursor: not-allowed; opacity: 0.85;">
                                        🟢 APROBADO Y RENOVADO
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #6b7280; padding: 2rem;">
                                No hay condominios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. CONFIGURACIÓN DE CUENTAS BANCARIAS YAPE / BCP / BBVA LIVO SAAS --}}
    <div class="master-card">
        <form wire:submit.prevent="guardarConfiguracionBancaria" class="space-y-4">
            {{ $this->form }}

            <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; border-radius: 0.75rem; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                    💾 GUARDAR CUENTAS BANCARIAS LIVO SAAS
                </button>
            </div>
        </form>
    </div>
</x-filament-panels::page>