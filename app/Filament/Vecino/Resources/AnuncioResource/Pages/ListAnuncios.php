<?php

namespace App\Filament\Vecino\Resources\AnuncioResource\Pages;

use App\Filament\Vecino\Resources\AnuncioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListAnuncios extends ListRecords
{
    protected static string $resource = AnuncioResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA ORIENTADA AL RESIDENTE CON BOTÓN A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🛍️',
            'badge'       => 'Mercadito Vecinal',
            'title'       => 'Marketplace & Venta entre Vecinos',
            'description' => 'Compra y vende productos o servicios de forma segura directamente con tus vecinos del edificio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Publicar Anuncio')
                ->createAnother(false),
        ];
    }
}