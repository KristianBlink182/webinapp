<?php

namespace App\Filament\Resources\ContactoEmergenciaResource\Pages;

use App\Filament\Resources\ContactoEmergenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactoEmergencia extends EditRecord
{
    protected static string $resource = ContactoEmergenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
