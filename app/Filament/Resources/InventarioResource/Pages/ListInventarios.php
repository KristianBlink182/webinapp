<?php

namespace App\Filament\Resources\InventarioResource\Pages;

use App\Filament\Resources\InventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListInventarios extends ListRecords
{
    protected static string $resource = InventarioResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📦',
            'badge'       => 'Activos y Patrimonio',
            'title'       => 'Inventario de Activos & Suministros',
            'description' => 'Control de herramientas, materiales de limpieza, luces de repuesto y activos fijos propiedad del condominio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Activo / Suministro')
                ->createAnother(false),
        ];
    }
}