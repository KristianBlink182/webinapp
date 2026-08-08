<?php

namespace App\Filament\Resources\MascotaResource\Pages;

use App\Filament\Resources\MascotaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListMascotas extends ListRecords
{
    protected static string $resource = MascotaResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🐾',
            'badge'       => 'Padrón de Mascotas',
            'title'       => 'Registro & Padrón de Mascotas del Edificio',
            'description' => 'Directorio oficial de mascotas registradas por departamento para la seguridad e identificación en el edificio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}