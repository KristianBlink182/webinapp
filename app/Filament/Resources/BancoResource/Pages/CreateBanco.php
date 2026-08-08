<?php

namespace App\Filament\Resources\BancoResource\Pages;

use App\Filament\Resources\BancoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBanco extends CreateRecord
{
    protected static string $resource = BancoResource::class;

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