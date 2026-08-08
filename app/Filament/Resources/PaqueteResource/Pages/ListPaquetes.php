<?php

namespace App\Filament\Resources\PaqueteResource\Pages;

use App\Filament\Resources\PaqueteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListPaquetes extends ListRecords
{
    protected static string $resource = PaqueteResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📦',
            'badge'       => 'Seguridad y Recepción',
            'title'       => 'Supervisión de Paquetes & Delivery',
            'description' => 'Directorio de encomiendas recepcionadas en portería y estado de entrega a los vecinos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}