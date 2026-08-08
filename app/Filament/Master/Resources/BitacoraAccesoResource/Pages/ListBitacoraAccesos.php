<?php

namespace App\Filament\Master\Resources\BitacoraAccesoResource\Pages;

use App\Filament\Master\Resources\BitacoraAccesoResource;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraAccesos extends ListRecords
{
    protected static string $resource = BitacoraAccesoResource::class;
    protected static ?string $title = 'Bitácora de Inicios de Sesión';
}