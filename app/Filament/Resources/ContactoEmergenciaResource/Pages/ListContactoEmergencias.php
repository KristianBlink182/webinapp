<?php

namespace App\Filament\Resources\ContactoEmergenciaResource\Pages;

use App\Filament\Resources\ContactoEmergenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListContactoEmergencias extends ListRecords
{
    protected static string $resource = ContactoEmergenciaResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN AGREGAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📞',
            'badge'       => 'Directorio de Auxilio',
            'title'       => 'Números de Emergencia Externa',
            'description' => 'Directorio oficial de bomberos, comisaría local, serenazgo y técnicos de auxilio para los vecinos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Agregar Contacto')
                ->createAnother(false)
                ->visible(fn () => strtolower(auth()->user()->role ?? '') === 'admin'),
        ];
    }
}