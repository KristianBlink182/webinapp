<?php

namespace App\Filament\Porteria\Widgets;

use App\Models\Visita;
use App\Models\Paquete;
use App\Models\AlertaSOS;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Notifications\Notification;

class PorteriaStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $visitasDentro = Visita::where('estado', 'Dentro')->count();
        $paquetesPendientes = Paquete::where('estado', 'Pendiente')->count();

        return [
            Stat::make('Visitas en el Edificio', $visitasDentro)
                ->description('Personas dentro actualmente')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($visitasDentro > 0 ? 'info' : 'gray'),

            Stat::make('Paquetes en Recepción', $paquetesPendientes)
                ->description('Pendientes por entregar a vecinos')
                ->descriptionIcon('heroicon-m-cube')
                ->color($paquetesPendientes > 0 ? 'warning' : 'success'),

            Stat::make('URGENCIA MÁXIMA', '🚨 ALERTA SISMO')
                ->description('Haga clic para emitir evacuación')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('danger')
                ->extraAttributes([
                    'wire:click' => 'activarAlertaSismo',
                    'style' => 'cursor: pointer; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff;',
                ]),
        ];
    }

    public function activarAlertaSismo(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->departamento?->condominio_id ?? 1;

        AlertaSOS::create([
            'condominio_id' => $condoId,
            'user_id' => auth()->id(),
            'tipo' => 'Sismo',
            'descripcion' => 'Alerta de Evacuación General por Sismo',
            'estado' => 'Pendiente',
        ]);

        Notification::make()
            ->title('🚨 ALERTA SÍSMICA Y EVACUACIÓN ACTIVADA')
            ->body('Se ha enviado la alerta de evacuación a todos los vecinos del edificio.')
            ->danger()
            ->persistent()
            ->send();
    }
}