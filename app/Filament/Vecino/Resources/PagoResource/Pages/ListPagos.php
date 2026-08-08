<?php

namespace App\Filament\Vecino\Resources\PagoResource\Pages;

use App\Filament\Vecino\Resources\PagoResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListPagos extends ListRecords
{
    protected static string $resource = PagoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA ORIENTADA AL RESIDENTE
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '💳',
            'badge'       => 'Mi Cuenta & Finanzas',
            'title'       => 'Mis Pagos & Recibos de Mantenimiento',
            'description' => 'Consulta tus estados de cuenta mensuales, descarga tu recibo oficial en PDF y sube tu comprobante Yape/Plin.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }
}