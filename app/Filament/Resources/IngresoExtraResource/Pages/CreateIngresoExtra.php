<?php

namespace App\Filament\Resources\IngresoExtraResource\Pages;

use App\Filament\Resources\IngresoExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIngresoExtra extends CreateRecord
{
    protected static string $resource = IngresoExtraResource::class;

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