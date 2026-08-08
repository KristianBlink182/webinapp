<?php

namespace App\Filament\Resources\VotacionResource\Pages;

use App\Filament\Resources\VotacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVotacion extends CreateRecord
{
    protected static string $resource = VotacionResource::class;

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