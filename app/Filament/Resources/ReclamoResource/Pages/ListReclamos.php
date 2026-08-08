<?php

namespace App\Filament\Resources\ReclamoResource\Pages;

use App\Filament\Resources\ReclamoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListReclamos extends ListRecords
{
    protected static string $resource = ReclamoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '💬',
            'badge'       => 'Atención al Vecino',
            'title'       => 'Gestión & Atención de Reclamos del Edificio',
            'description' => 'Revisión de incidencias reportadas por los residentes, seguimiento de estado y respuestas directas.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}