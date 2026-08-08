<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use App\Models\Pago;
use App\Models\IngresoExtra;
use App\Models\Reclamo;
use App\Models\Visita;
use App\Models\Paquete;
use App\Models\Departamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminDashboard extends BaseWidget
{
    protected static ?int $sort = 1;

  public static function canView(): bool
    {
        return false; // 🎯 OCULTA ESTE WIDGET ANTIGUO DEL ESCRITORIO
    }

    protected function getStats(): array
    {
        // --- BLOQUE FINANCIERO ---
        $ingresos = Pago::where('estado', 'Pagado')->sum('monto') ?? 0;
        $extras = IngresoExtra::where('estado', 'Pagado')->sum('monto') ?? 0;
        $egresos = Gasto::sum('monto') ?? 0;
        $deudaDinero = Pago::where('estado', 'Pendiente')->sum('monto') ?? 0;
        $saldo = ($ingresos + $extras) - $egresos;

        // --- BLOQUE OPERATIVO (NUEVAS ETIQUETAS) ---
        // 1. Conteo de morosos (Departamentos únicos que deben)
        $conteoMorosos = Pago::where('estado', 'Pendiente')->distinct('departamento_id')->count('departamento_id');
        
        // 2. Reclamos sin resolver
        $reclamosAbiertos = Reclamo::where('estado', '!=', 'Resuelto')->count();

        // 3. Visitas dentro del edificio (sin fecha de salida)
        $visitasAdentro = Visita::whereNull('fecha_salida')->count();

        // 4. Paquetes en recepción
        $paquetesPendientes = Paquete::where('estado', 'En Recepción')->count();

        return [
            // FILA 1: DINERO
            Stat::make('Saldo Disponible', 'S/ ' . number_format($saldo, 2))
                ->description('Fondos netos en caja')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([7, 3, 5, 3, 8, 2, 10]),

            Stat::make('Deuda por Cobrar', 'S/ ' . number_format($deudaDinero, 2))
                ->description($conteoMorosos . ' departamentos pendientes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            // FILA 2: OPERACIÓN Y SEGURIDAD
            Stat::make('Visitas Activas', $visitasAdentro)
                ->description('Personas dentro ahora')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Reclamos / Incidencias', $reclamosAbiertos)
                ->description('Pendientes de atención')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($reclamosAbiertos > 0 ? 'warning' : 'success'),

            Stat::make('Paquetes en Portería', $paquetesPendientes)
                ->description('Pendientes de entrega')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('emerald'),
        ];
    }
}