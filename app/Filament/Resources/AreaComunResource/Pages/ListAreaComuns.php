<?php

namespace App\Filament\Resources\AreaComunResource\Pages;

use App\Filament\Resources\AreaComunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListAreaComuns extends ListRecords
{
    protected static string $resource = AreaComunResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON BOTÓN CREAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🏊',
            'badge'       => 'Gestión de Espacios',
            'title'       => 'Áreas Comunes & Espacios del Edificio',
            'description' => 'Configuración de parrillas, SUM, piscina, gimnasio, capacidad máxima y reglas de uso.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Área Común')
                ->createAnother(false),
        ];
    }
}