<?php

namespace App\Filament\Resources\ComunicadoResource\Pages;

use App\Filament\Resources\ComunicadoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComunicado extends CreateRecord
{
    protected static string $resource = ComunicadoResource::class;

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