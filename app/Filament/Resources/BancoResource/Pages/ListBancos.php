<?php

namespace App\Filament\Resources\BancoResource\Pages;

use App\Filament\Resources\BancoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListBancos extends ListRecords
{
    protected static string $resource = BancoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON EL BOTÓN AGREGAR A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🏦',
            'badge'       => 'Tesorería y Bancos',
            'title'       => 'Cuentas Bancarias Oficiales del Edificio',
            'description' => 'Registro de cuentas BCP, BBVA, Interbank y Yape/Plin para la cobranza de mantenimientos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Agregar Cuenta Bancaria')
                ->createAnother(false),
        ];
    }
}