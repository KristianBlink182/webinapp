<?php

namespace App\Filament\Vecino\Resources\AlertaSOSResource\Pages;

use App\Filament\Vecino\Resources\AlertaSOSResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlertaSOS extends ListRecords
{
    protected static string $resource = AlertaSOSResource::class;
    protected static ?string $title = 'Alertas S O S';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Emitir Alerta SOS'),
        ];
    }
}