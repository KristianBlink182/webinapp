<?php

namespace App\Filament\Widgets;

use App\Models\Pago;
use App\Models\Gasto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!$tenant) {
            return [];
        }

        // 1. Recaudado este Mes
        $recaudadoMes = Pago::whereHas('departamento', function ($query) use ($tenant) {
            $query->where('condominio_id', $tenant->id);
        })
        ->whereIn('estado', ['Aprobado', 'Pagado', 'pagado', 'aprobado'])
        ->whereMonth('created_at', now()->month)
        ->sum('monto') ?? 0;

        // 2. Gastos del Mes
        $gastosMes = Gasto::where('condominio_id', $tenant->id)
            ->where(function ($q) {
                $q->whereMonth('fecha_gasto', now()->month)
                  ->orWhereMonth('created_at', now()->month);
            })
            ->sum('monto') ?? 0;

        // 3. Deuda Total Pendiente (Morosidad)
        $morosidadTotal = Pago::whereHas('departamento', function ($query) use ($tenant) {
            $query->where('condominio_id', $tenant->id);
        })
        ->whereIn('estado', ['Pendiente', 'pendiente', 'Pendiente de Pago'])
        ->sum('monto') ?? 0;

        $depasMorosos = Pago::whereHas('departamento', function ($query) use ($tenant) {
            $query->where('condominio_id', $tenant->id);
        })
        ->whereIn('estado', ['Pendiente', 'pendiente', 'Pendiente de Pago'])
        ->distinct('departamento_id')
        ->count('departamento_id');

        // 4. Saldo Neto Real en Caja
        $totalHistoricoCobrado = Pago::whereHas('departamento', function ($query) use ($tenant) {
            $query->where('condominio_id', $tenant->id);
        })
        ->whereIn('estado', ['Aprobado', 'Pagado', 'pagado', 'aprobado'])
        ->sum('monto') ?? 0;

        $totalHistoricoEgresos = Gasto::where('condominio_id', $tenant->id)->sum('monto') ?? 0;

        $saldoCaja = $totalHistoricoCobrado - $totalHistoricoEgresos;

        return [
            Stat::make('Recaudado este Mes', 'S/ ' . number_format($recaudadoMes, 2))
                ->description('Pagos confirmados este mes')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([10, 20, 30, $recaudadoMes]),

            Stat::make('Gastos del Mes', 'S/ ' . number_format($gastosMes, 2))
                ->description('Egresos del edificio este mes')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($gastosMes > 0 ? 'warning' : 'gray'),

            Stat::make('Morosidad Pendiente', 'S/ ' . number_format($morosidadTotal, 2))
                ->description($depasMorosos . ' departamentos pendientes')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($morosidadTotal > 0 ? 'danger' : 'success'),

            Stat::make('Saldo Real en Caja', 'S/ ' . number_format($saldoCaja, 2))
                ->description($saldoCaja >= 0 ? 'Fondos reales disponibles' : 'Déficit presupuestal')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($saldoCaja >= 0 ? 'success' : 'danger')
                ->chart([$saldoCaja > 0 ? $saldoCaja : 0]),
        ];
    }
}