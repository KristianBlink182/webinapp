<?php

namespace App\Filament\Master\Resources\CondominioResource\Pages;

use App\Filament\Master\Resources\CondominioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCondominios extends ListRecords
{
    protected static string $resource = CondominioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
