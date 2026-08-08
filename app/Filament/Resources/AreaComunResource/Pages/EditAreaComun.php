<?php

namespace App\Filament\Resources\AreaComunResource\Pages;

use App\Filament\Resources\AreaComunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAreaComun extends EditRecord
{
    protected static string $resource = AreaComunResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}