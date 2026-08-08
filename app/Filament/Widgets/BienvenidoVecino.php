<?php

namespace App\Filament\Widgets;

use App\Models\Pago;
use App\Models\Comunicado;
use Filament\Widgets\Widget;

class BienvenidoVecino extends Widget
{
    protected static string $view = 'filament.widgets.bienvenido-vecino';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role === 'residente';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        
        $deudaTotal = Pago::where('departamento_id', $user->departamento_id)
            ->where('estado', 'Pendiente')
            ->sum('monto') ?? 0;

        $ultimoAviso = Comunicado::where('condominio_id', $user->departamento?->condominio_id)
            ->latest()
            ->first();

        return [
            'deudaTotal' => $deudaTotal,
            'ultimoAviso' => $ultimoAviso,
            'departamento' => $user->departamento?->numero ?? 'N/A',
            'condominio' => $user->departamento?->condominio?->nombre ?? 'Edificio',
        ];
    }
}