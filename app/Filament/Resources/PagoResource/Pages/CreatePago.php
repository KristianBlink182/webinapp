<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePago extends CreateRecord
{
    protected static string $resource = PagoResource::class;

    // 🎯 REDIRECCIÓN DIRECTA A LA TABLA DE COBROS Y RECIBOS
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // 🎯 REMOVE EL BOTÓN "CREAR Y CREAR OTRO"
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}