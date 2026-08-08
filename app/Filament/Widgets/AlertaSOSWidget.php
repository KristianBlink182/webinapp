<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\AlertaSOS;
use App\Models\SismoCheckin;
use App\Models\Condominio;
use Filament\Notifications\Notification;

class AlertaSOSWidget extends Widget
{
    protected static string $view = 'filament.widgets.alerta-s-o-s-widget';
    protected static ?int $sort = -100;
    protected int | string | array $columnSpan = 'full';

   public function activarAlertaSismo(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        $condo = Condominio::find($condoId);
        if ($condo) {
            $condo->update(['sismo_activo' => true]);
            
            // LIMPIA RESPUESTAS DEL TEMBLOR ANTERIOR PARA EL NUEVO SISMO
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
            SismoCheckin::where('condominio_id', $condoId)->delete();
        }

        Notification::make()
            ->title('🟢 ALERTA SÍSMICA FINALIZADA')
            ->body('Se ha desactivado la alerta en la pantalla de todos los vecinos.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;
        $condo = Condominio::find($condoId);

        $alertasSOS = AlertaSOS::where('condominio_id', $condoId)
            ->where('estado', 'Pendiente')
            ->latest()
            ->get();

        $auxiliosSismo = SismoCheckin::where('condominio_id', $condoId)
            ->where('estado_seguridad', 'Necesito Ayuda')
            ->latest()
            ->get();

        return [
            'sismoActivo' => $condo?->sismo_activo ?? false,
            'alertasSOS' => $alertasSOS,
            'auxiliosSismo' => $auxiliosSismo,
        ];
    }
}