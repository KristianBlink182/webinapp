<?php

namespace App\Filament\Widgets;

use App\Models\Pago;
use App\Filament\Resources\PagoResource; 
use Filament\Widgets\Widget;

class CajaPagoWidget extends Widget
{
    protected static string $view = 'filament.widgets.caja-pago-widget';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role === 'residente';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        
        $deuda = Pago::where('departamento_id', $user->departamento_id)
            ->where('estado', 'Pendiente')
            ->first();

        // Intentar obtener la URL de edición o la lista principal de forma segura
        $urlPago = '#';
        if ($deuda) {
            try {
                $urlPago = PagoResource::getUrl('edit', ['record' => $deuda->id]);
            } catch (\Throwable $th) {
                try {
                    $urlPago = PagoResource::getUrl('index');
                } catch (\Throwable $th) {
                    $urlPago = '#';
                }
            }
        }

        return [
            'pago' => $deuda,
            'condominio' => $user->departamento?->condominio,
            'urlPago' => $urlPago,
        ];
    }
}