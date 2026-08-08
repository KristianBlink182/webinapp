<?php

namespace App\Filament\Vecino\Resources\ReservaResource\Pages;

use App\Filament\Vecino\Resources\ReservaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListReservas extends ListRecords
{
    protected static string $resource = ReservaResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA ORIENTADA AL RESIDENTE CON BOTÓN A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🏊',
            'badge'       => 'Reservas & Espacios',
            'title'       => 'Reserva de Áreas Comunes',
            'description' => 'Separa el uso de la parrilla, SUM, piscina o gimnasio y adjunta tu voucher de pago si el espacio lo requiere.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Reserva')
                ->createAnother(false),
        ];
    }
}