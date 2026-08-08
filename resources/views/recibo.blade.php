<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Mantenimiento - LIVO</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .logo-title {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-number {
            text-align: right;
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
        }
        .debt-box {
            border: 2px solid #ef4444;
            background: #fff5f5;
            padding: 12px 18px;
            text-align: center;
            border-radius: 8px;
        }
        .debt-title {
            font-size: 10px;
            font-weight: 800;
            color: #991b1b;
            text-transform: uppercase;
        }
        .debt-amount {
            font-size: 20px;
            font-weight: 900;
            color: #dc2626;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
        }
        .info-table td {
            padding: 4px 8px;
            font-size: 11px;
        }
        .label {
            font-weight: 700;
            color: #64748b;
            width: 100px;
        }
        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #334155;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-size: 11px;
        }
        .total-row {
            background: #f8fafc;
            font-weight: 800;
        }
        .footer-note {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $condo = $pago->departamento?->condominio;
        $depa = $pago->departamento;
        $montoTotal = $pago->monto ?? 0;
        $saldoAnterior = $pago->saldo_anterior ?? 0;
        $mora = $pago->monto_mora ?? 0;
        $numeroRecibo = str_pad($pago->id, 6, '0', STR_PAD_LEFT);
        $mesAnio = strtoupper($pago->mes ?? 'MES') . ' - ' . ($pago->anio ?? date('Y'));
    @endphp

    {{-- ENCABEZADO PRINCIPAL Y NÚMERO DE RECIBO --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if(!empty($condo?->logo))
                    <img src="{{ asset('storage/' . $condo->logo) }}" style="height: 45px; width: auto; margin-bottom: 5px;">
                @endif
                <div class="logo-title">{{ $condo?->nombre ?? 'Condominio LIVO' }}</div>
                <div style="font-size: 10px; color: #64748b;">Administración Inteligente de Condominios</div>
            </td>
            <td style="width: 45%; vertical-align: top;" class="doc-number">
                <div style="font-size: 14px; color: #0284c7; text-transform: uppercase; font-weight: 900;">
                    RECIBO DE MANTENIMIENTO
                </div>
                <div style="font-size: 12px; margin-top: 2px;">N° 001-{{ $numeroRecibo }}</div>
                <div style="font-size: 11px; color: #475569; font-weight: 800; margin-top: 2px;">{{ $mesAnio }}</div>
                <div style="font-size: 10px; color: #64748b; font-weight: 400; margin-top: 3px;">
                    Emisión: {{ date('d/m/Y', strtotime($pago->created_at)) }} &bull; Vencimiento: {{ $pago->fecha_vencimiento ? date('d/m/Y', strtotime($pago->fecha_vencimiento)) : '12 de este mes' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- DATOS DEL VECINO Y CAJA DE DEUDA PRÓXIMA --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <table class="info-table">
                    <tr>
                        <td class="label">Edificio:</td>
                        <td><strong>{{ $condo?->nombre ?? 'LIVO' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Departamento:</td>
                        <td><strong>Dpto. {{ $depa?->numero ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Residente:</td>
                        <td>{{ $depa?->nombre_propietario ?? $depa?->nombre_inquilino ?? auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td>{{ $condo?->direccion ?? 'Av. Principal' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; vertical-align: top; padding-left: 15px;">
                <div class="debt-box">
                    <div class="debt-title">Usted tiene una deuda próxima a vencer de:</div>
                    <div class="debt-amount">S/ {{ number_format($montoTotal, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- A. SALDO ANTERIOR --}}
    <div class="section-title">A. Saldo Anterior</div>
    <table class="data-table">
        <tr>
            <td>Usted cuenta con una deuda anterior a la emisión de este recibo de:</td>
            <td style="width: 120px; text-align: right; font-weight: 800;">S/ {{ number_format($saldoAnterior, 2) }}</td>
        </tr>
    </table>

    {{-- B. CONCEPTOS DE LA CUOTA DEL MES --}}
    <div class="section-title">B. Conceptos de la Cuota del Mes ({{ $mesAnio }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Descripción del Concepto</th>
                <th style="width: 120px; text-align: right;">Monto (S/)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cuota de Mantenimiento Base (Luz áreas comunes, limpieza, seguridad)</td>
                <td style="text-align: right;">S/ {{ number_format($pago->monto_mantenimiento ?? $montoTotal, 2) }}</td>
            </tr>
            @if(!empty($pago->monto_luz) && $pago->monto_luz > 0)
                <tr>
                    <td>Consumo de Luz Común / Ascensores</td>
                    <td style="text-align: right;">S/ {{ number_format($pago->monto_luz, 2) }}</td>
                </tr>
            @endif
            @if(!empty($pago->monto_agua) && $pago->monto_agua > 0)
                <tr>
                    <td>Consumo de Agua por Medidor (Lectura Actual: {{ $pago->lectura_actual ?? 'N/A' }})</td>
                    <td style="text-align: right;">S/ {{ number_format($pago->monto_agua, 2) }}</td>
                </tr>
            @endif
            @if(!empty($mora) && $mora > 0)
                <tr>
                    <td>Interés por Atraso / Mora de Pago</td>
                    <td style="text-align: right;">S/ {{ number_format($mora, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td style="text-align: right;">TOTAL A ABONAR:</td>
                <td style="text-align: right; color: #0284c7; font-size: 13px;">S/ {{ number_format($montoTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- (A+B). ESTADO DE CUENTA ACTUAL --}}
    <div class="section-title">(A+B). Estado de Cuenta Actual</div>
    <table class="data-table">
        <tr>
            <td>Deuda por Vencer (Recibo del mes):</td>
            <td style="width: 120px; text-align: right;">S/ {{ number_format($montoTotal, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td style="text-align: right;">DEUDA TOTAL A LA FECHA:</td>
            <td style="text-align: right; color: #dc2626; font-size: 13px;">S/ {{ number_format($saldoAnterior + $montoTotal, 2) }}</td>
        </tr>
    </table>

    {{-- INFORMACIÓN DE PAGO BANCARIO / YAPE / PLIN --}}
    @if(!empty($condo?->instrucciones_banco) || !empty($condo?->yape_numero))
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-top: 15px;">
            <div style="font-weight: 800; font-size: 11px; color: #0f172a; margin-bottom: 4px;">Información para el Pago:</div>
            <div style="font-size: 10px; color: #475569; line-height: 1.5;">
                {!! nl2br(e($condo->instrucciones_banco ?? '')) !!}
                @if(!empty($condo->yape_numero))
                    <br><strong>Yape / Plin Condominio:</strong> {{ $condo->yape_numero }}
                @endif
                <br><em>Realizar transferencia a las cuentas del condominio y subir la captura de pantalla a través de la app LIVO Vecinos.</em>
            </div>
        </div>
    @endif

    {{-- PIE DE PÁGINA --}}
    <div class="footer-note">
        Este documento es un comprobante oficial emitido por la plataforma LIVO SaaS &bull; Administración Inteligente de Condominios.
    </div>

</body>
</html>