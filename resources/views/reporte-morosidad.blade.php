<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #222; }
        .header { text-align: center; border-bottom: 3px solid #f43f5e; padding-bottom: 10px; }
        .title { font-size: 22px; font-weight: bold; color: #f43f5e; margin: 10px 0; }
        .edificio { font-size: 16px; color: #666; }
        .fecha { text-align: right; font-size: 12px; color: #999; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 13px; }
        td { border: 1px solid #e2e8f0; padding: 10px; font-size: 13px; }
        .resaltado { font-weight: bold; color: #e11d48; }
        .footer { margin-top: 30px; font-size: 11px; text-align: center; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE MOROSIDAD</div>
        <div class="edificio">{{ $condominio->nombre }}</div>
    </div>

    <div class="fecha">Fecha de emisión: {{ date('d/m/Y') }}</div>

    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Propietario</th>
                <th>Meses Pendientes</th>
                <th>Deuda Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGlobal = 0; @endphp
            @foreach($deudores as $deudor)
                <tr>
                    <td>{{ $deudor['numero'] }}</td>
                    <td>{{ $deudor['propietario'] }}</td>
                    <td>{{ $deudor['meses'] }}</td>
                    <td class="resaltado">S/ {{ number_format($deudor['total'], 2) }}</td>
                </tr>
                @php $totalGlobal += $deudor['total']; @endphp
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold;">
        TOTAL POR COBRAR: S/ {{ number_format($totalGlobal, 2) }}
    </div>

    <div class="footer">
        Este documento es un reporte informativo generado por NextGen Condos.<br>
        Por favor, si ya realizó su pago, ignore este reporte y envíe su voucher a la administración.
    </div>
</body>
</html>