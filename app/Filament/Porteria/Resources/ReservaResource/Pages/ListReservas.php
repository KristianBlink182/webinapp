<?php

namespace App\Filament\Porteria\Resources\ReservaResource\Pages;

use App\Filament\Porteria\Resources\ReservaResource;
use Filament\Resources\Pages\ListRecords;

class ListReservas extends ListRecords
{
    protected static string $resource = ReservaResource::class;
    protected static ?string $title = 'Agenda de Uso de Áreas Comunes';
}