<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO OSCURO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🛡️',
            'badge'       => 'Seguridad & Auditoría',
            'title'       => 'Historial de Acciones & Bitácora del Edificio',
            'description' => 'Registro inalterable de movimientos, creaciones, modificaciones y eliminaciones realizadas por los usuarios.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}