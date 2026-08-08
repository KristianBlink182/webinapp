<?php

namespace App\Filament\Resources\DocumentoResource\Pages;

use App\Filament\Resources\DocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListDocumentos extends ListRecords
{
    protected static string $resource = DocumentoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON BOTÓN CREAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📚',
            'badge'       => 'Biblioteca Virtual',
            'title'       => 'Biblioteca de Documentos Oficiales',
            'description' => 'Repositorio de reglamentos internos, actas de asamblea, informes financieros y manuales del edificio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Documento')
                ->createAnother(false),
        ];
    }
}