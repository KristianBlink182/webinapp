<?php

namespace App\Filament\Resources\GastoResource\Pages;

use App\Filament\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListGastos extends ListRecords
{
    protected static string $resource = GastoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN CREAR GASTO A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📉',
            'badge'       => 'Finanzas del Edificio',
            'title'       => 'Control de Gastos & Egresos del Condominio',
            'description' => 'Registro de facturas, boletas de luz/agua común y pagos a proveedores para el cálculo de mantenimientos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Gasto')
                ->createAnother(false),
        ];
    }
}