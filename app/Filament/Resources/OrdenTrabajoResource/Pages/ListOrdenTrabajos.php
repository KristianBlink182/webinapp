<?php

namespace App\Filament\Resources\OrdenTrabajoResource\Pages;

use App\Filament\Resources\OrdenTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListOrdenTrabajos extends ListRecords
{
    protected static string $resource = OrdenTrabajoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🛠️',
            'badge'       => 'Gestión Operativa',
            'title'       => 'Órdenes de Trabajo & Reparaciones',
            'description' => 'Asignación y seguimiento de trabajos técnicos de reparación contratados para el edificio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Orden de Trabajo')
                ->createAnother(false),
        ];
    }
}