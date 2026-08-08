<?php

namespace App\Filament\Master\Widgets;

use App\Models\Condominio;
use App\Models\User;
use App\Models\Pago;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalCondominios = Condominio::count();
        $totalUsuarios = User::count();
        $totalRecaudadoGlobal = Pago::where('estado', 'Aprobado')->sum('monto') ?? 0;

        return [
            Stat::make('Condominios Activos', $totalCondominios)
                ->description('Edificios registrados en LIVO')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([3, 5, 8, 12, 15, $totalCondominios]),

            Stat::make('Usuarios Totales', $totalUsuarios)
                ->description('Vecinos, Admins y Vigilantes')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([10, 25, 40, $totalUsuarios]),

            Stat::make('Volumen Recaudado', 'S/ ' . number_format($totalRecaudadoGlobal, 2))
                ->description('Procesado globalmente en la plataforma')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}