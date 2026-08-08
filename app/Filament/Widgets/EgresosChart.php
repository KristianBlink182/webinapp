<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EgresosChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Gastos Mensuales del Edificio';
    protected static string $color = 'danger';

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $role = auth()->user()->role;

        return in_array($role, ['admin', 'administrador', 'super_admin', 'superadmin', 'master']);
    }

    protected function getData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id;

        $gastosPorMes = Gasto::where('condominio_id', $condoId)
            ->select(
                DB::raw("strftime('%m', fecha_gasto) as mes"),
                DB::raw("SUM(monto) as total")
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $valores = [];
        for ($i = 1; $i <= 12; $i++) {
            $mesKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $valores[] = $gastosPorMes[$mesKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Egresos (S/)',
                    'data' => $valores,
                    'backgroundColor' => '#f43f5e',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $mesesNombres,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}