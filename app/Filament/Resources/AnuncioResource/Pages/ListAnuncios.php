<?php

namespace App\Filament\Resources\AnuncioResource\Pages;

use App\Filament\Resources\AnuncioResource;
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

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN VENDER ALGO A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🛍️',
            'badge'       => 'Mercadito Vecinal',
            'title'       => 'Supervisión de Anuncios & Marketplace',
            'description' => 'Moderación de productos y servicios publicados por los residentes del condominio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Vender Algo')
                ->createAnother(false),
        ];
    }
}