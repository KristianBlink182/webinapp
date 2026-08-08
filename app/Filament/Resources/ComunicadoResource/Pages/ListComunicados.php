<?php

namespace App\Filament\Resources\ComunicadoResource\Pages;

use App\Filament\Resources\ComunicadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListComunicados extends ListRecords
{
    protected static string $resource = ComunicadoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON BOTÓN PUBLICAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📢',
            'badge'       => 'Comunicación Oficial',
            'title'       => 'Muro de Avisos & Comunicados del Edificio',
            'description' => 'Publicación de avisos generales, alertas de mantenimiento y comunicados urgentes para los vecinos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Publicar Nuevo Comunicado')
                ->icon('heroicon-m-megaphone')
                ->createAnother(false),
        ];
    }
}