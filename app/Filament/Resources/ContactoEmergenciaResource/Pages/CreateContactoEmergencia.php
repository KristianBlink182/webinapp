<?php

namespace App\Filament\Resources\ContactoEmergenciaResource\Pages;

use App\Filament\Resources\ContactoEmergenciaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactoEmergencia extends CreateRecord
{
    protected static string $resource = ContactoEmergenciaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}