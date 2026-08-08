<?php

namespace App\Filament\Resources\VisitaResource\Pages;

use App\Filament\Resources\VisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListVisitas extends ListRecords
{
    protected static string $resource = VisitaResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🚶',
            'badge'       => 'Control de Accesos',
            'title'       => 'Supervisión de Visitas e Ingresos',
            'description' => 'Historial de accesos en tiempo real registrados por la portería y pre-autorizaciones de los residentes.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}