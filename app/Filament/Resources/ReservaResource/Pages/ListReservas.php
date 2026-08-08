<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
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

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '📅',
            'badge'       => 'Finanzas & Áreas Comunes',
            'title'       => 'Aprobación de Reservas & Verificación de Vouchers',
            'description' => 'Validación de comprobantes de pago Yape/Plin/Transferencia y confirmación de uso de zonas comunes.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}