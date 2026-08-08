<?php

namespace App\Filament\Porteria\Resources\PaqueteResource\Pages;

use App\Filament\Porteria\Resources\PaqueteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaquetes extends ListRecords
{
    protected static string $resource = PaqueteResource::class;
    protected static ?string $title = 'Recepción de Paquetes y Delivery';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Recibir Nuevo Paquete')
                ->createAnother(false), // 🎯 QUITA "CREAR Y CREAR OTRO" Y VA DIRECTO A LA TABLA
        ];
    }
}