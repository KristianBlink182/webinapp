<?php

namespace App\Filament\Vecino\Resources\ComunicadoResource\Pages;

use App\Filament\Vecino\Resources\ComunicadoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListComunicados extends ListRecords
{
    protected static string $resource = ComunicadoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON TONO VECINO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📢',
            'badge'       => 'Comunicación Oficial',
            'title'       => 'Muro de Avisos & Comunicados del Edificio',
            'description' => 'Avisos oficiales, noticias e informaciones publicadas por la Administración.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }
}