<?php

namespace App\Filament\Vecino\Resources\MascotaResource\Pages;

use App\Filament\Vecino\Resources\MascotaResource;
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

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON BOTÓN REGISTRAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🐾',
            'badge'       => 'Padrón de Mascotas',
            'title'       => 'Registro de Mis Mascotas',
            'description' => 'Mantén actualizado el registro de tus mascotas para la seguridad e identificación en el edificio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Mascota')
                ->createAnother(false),
        ];
    }
}