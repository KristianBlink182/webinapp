<?php

namespace App\Filament\Resources\ProveedorResource\Pages;

use App\Filament\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListProveedors extends ListRecords
{
    protected static string $resource = ProveedorResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🏭',
            'badge'       => 'Proveedores y Técnicos',
            'title'       => 'Directorio Oficial de Proveedores',
            'description' => 'Directorio de empresas contratistas, empresas de mantenimiento, gasfitería, ascensores y servicios externos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Proveedor')
                ->createAnother(false),
        ];
    }
}