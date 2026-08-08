<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPago extends EditRecord
{
    protected static string $resource = PagoResource::class;

    // 1. CAMBIAR EL TÍTULO DE LA PESTAÑA
    public function getTitle(): string
    {
        return auth()->user()->role === 'residente' 
            ? 'Reportar mi Pago' 
            : 'Editar Recibo de Pago';
    }

    // 2. CAMBIAR EL TEXTO DEL BOTÓN DE GUARDAR
    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label(auth()->user()->role === 'residente' ? 'Enviar Reporte de Pago' : 'Guardar Cambios');
    }

    // 3. CAMBIAR EL TEXTO DEL BOTÓN CANCELAR
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Volver atrás');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn() => auth()->user()->role === 'admin'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}