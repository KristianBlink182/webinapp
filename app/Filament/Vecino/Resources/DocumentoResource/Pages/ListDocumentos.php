<?php

namespace App\Filament\Vecino\Resources\DocumentoResource\Pages;

use App\Filament\Vecino\Resources\DocumentoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListDocumentos extends ListRecords
{
    protected static string $resource = DocumentoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON TONO VECINO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📚',
            'badge'       => 'Repositorio Virtual',
            'title'       => 'Biblioteca de Documentos del Edificio',
            'description' => 'Consulta y descarga el reglamento interno, actas de asamblea e informes del condominio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }
}