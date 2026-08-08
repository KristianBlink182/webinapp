<?php

namespace App\Filament\Vecino\Resources\ReclamoResource\Pages;

use App\Filament\Vecino\Resources\ReclamoResource;
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

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON BOTÓN ENVIAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '💬',
            'badge'       => 'Atención al Residente',
            'title'       => 'Reclamos, Reportes & Sugerencias',
            'description' => 'Envía tus consultas o incidencias a la administración y dale seguimiento en tiempo real.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Enviar Reclamo / Reporte')
                ->createAnother(false),
        ];
    }
}