<?php

namespace App\Filament\Vecino\Resources\VotacionResource\Pages;

use App\Filament\Vecino\Resources\VotacionResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListVotaciones extends ListRecords
{
    protected static string $resource = VotacionResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON TONO VECINO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🗳️',
            'badge'       => 'Decisiones de la Directiva',
            'title'       => 'Votaciones & Acuerdos de la Comunidad',
            'description' => 'Participa en las consultas de la junta de propietarios, revisa los sustentos técnicos y emite tu voto.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }
}