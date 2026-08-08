<?php

namespace App\Filament\Resources\IngresoExtraResource\Pages;

use App\Filament\Resources\IngresoExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListIngresoExtras extends ListRecords
{
    protected static string $resource = IngresoExtraResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN CREAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '➕',
            'badge'       => 'Finanzas del Edificio',
            'title'       => 'Multas & Ingresos Extraordinarios',
            'description' => 'Gestión de sanciones por desacato a normas, alquileres de espacios y aportes extraordinarios.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Ingreso Extra / Multa')
                ->createAnother(false),
        ];
    }
}