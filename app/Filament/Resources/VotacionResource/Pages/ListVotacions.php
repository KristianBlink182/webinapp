<?php

namespace App\Filament\Resources\VotacionResource\Pages;

use App\Filament\Resources\VotacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListVotacions extends ListRecords
{
    protected static string $resource = VotacionResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN CREAR VOTACIÓN A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🗳️',
            'badge'       => 'Directiva del Edificio',
            'title'       => 'Votaciones & Consultas a la Comunidad',
            'description' => 'Creación de consultas populares, asignación de opciones de respuesta y auditoría de resultados en tiempo real.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Votación')
                ->createAnother(false),
        ];
    }
}