<?php

namespace App\Filament\Resources\MantenimientoResource\Pages;

use App\Filament\Resources\MantenimientoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMantenimiento extends CreateRecord
{
    protected static string $resource = MantenimientoResource::class;

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