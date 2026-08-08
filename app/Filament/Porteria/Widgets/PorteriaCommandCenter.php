<?php

namespace App\Filament\Porteria\Widgets;

use Filament\Widgets\Widget;
use App\Models\SismoCheckin;
use App\Models\AlertaSOS;
use App\Models\Visita;
use App\Models\Paquete;
use App\Models\Condominio;
use Filament\Notifications\Notification;

class PorteriaCommandCenter extends Widget
{
    protected static string $view = 'filament.porteria.widgets.porteria-command-center';
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function activarAlertaSismo(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        $condo = Condominio::find($condoId);
        if ($condo) {
            $condo->update(['sismo_activo' => true]);
            
            // LIMPIA LAS RESPUESTAS DEL TEMBLOR ANTERIOR PARA QUE TODOS LOS VECINOS RECIBAN LA NUEVA ALERTA
            SismoCheckin::where('condominio_id', $condoId)->delete();
        }

        Notification::make()
            ->title('🚨 ALERTA SÍSMICA ACTIVADA')
            ->body('Se ha activado la orden de evacuación para todo el edificio.')
            ->danger()
            ->send();
    }

    public function finalizarAlertaSismo(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        $condo = Condominio::find($condoId);
        if ($condo) {
            $condo->update(['sismo_activo' => false]);
            // SE PRESERVA EL HISTORIAL INTACTO SIN BORRAR REGISTROS DE AUDITORÍA
        }

        Notification::make()
            ->title('🟢 ALERTA SÍSMICA FINALIZADA')
            ->body('Se ha desactivado la alerta en la pantalla de todos los vecinos.')
            ->success()
            ->send();
    }

    public function atenderSOS(int $sosId): void
    {
        $sos = AlertaSOS::find($sosId);
        if ($sos) {
            $sos->update([
                'estado' => 'Atendido',
                'fecha_atendido' => \Carbon\Carbon::now('America/Lima')->format('Y-m-d H:i:s'),
            ]);

            Notification::make()->title('Alerta SOS Atendida')->success()->send();
        }
    }

    public function atenderAuxilioSismo(int $checkinId): void
    {
        $checkin = SismoCheckin::find($checkinId);
        if ($checkin) {
            $checkin->update([
                'estado_seguridad' => 'Atendido',
                'fecha_atendido' => \Carbon\Carbon::now('America/Lima')->format('Y-m-d H:i:s'),
            ]);

            Notification::make()->title('🟢 Auxilio de Sismo Atendido')->success()->send();
        }
    }

    protected function getViewData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        return [
            'sismoActivo' => $tenant?->sismo_activo ?? false,
            'visitasDentro' => Visita::where('condominio_id', $condoId)->where('estado_visita', 'Dentro')->count(),
            'paquetesPendientes' => Paquete::where('condominio_id', $condoId)->where('estado', 'En Recepción')->count(),
            'alertasSOS' => AlertaSOS::where('condominio_id', $condoId)->where('estado', 'Pendiente')->latest()->get(),
            'auxiliosSismo' => SismoCheckin::where('condominio_id', $condoId)->where('estado_seguridad', 'Necesito Ayuda')->latest()->get(),
            
            // PRESERVA Y MUESTRA EL HISTORIAL PERMANENTE DE VECINOS A SALVO
            'confirmadosASalvo' => SismoCheckin::where('condominio_id', $condoId)->whereIn('estado_seguridad', ['Estoy Bien', 'A Salvo', 'A salvo'])->latest()->get(),
            
            'historialSOS' => AlertaSOS::where('condominio_id', $condoId)->latest()->take(10)->get(),
        ];
    }
}