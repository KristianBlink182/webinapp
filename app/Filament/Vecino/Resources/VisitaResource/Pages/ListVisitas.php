<?php

namespace App\Filament\Vecino\Resources\VisitaResource\Pages;

use App\Filament\Vecino\Resources\VisitaResource;
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

    // 🏛️ CABECERA EJECUTIVA ORIENTADA AL RESIDENTE CON BOTÓN A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '👥',
            'badge'       => 'Seguridad & Accesos',
            'title'       => 'Pre-Autorización de Invitados & Pases',
            'description' => 'Anuncia a tus familiares, amigos o delivery para que la portería les dé acceso directo e inmediato al llegar.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Anunciar Nuevo Invitado')
                ->createAnother(false),
        ];
    }
}