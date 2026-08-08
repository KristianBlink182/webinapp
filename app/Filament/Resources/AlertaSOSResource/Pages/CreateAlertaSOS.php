<?php

namespace App\Filament\Resources\AlertaSOSResource\Pages;

use App\Filament\Resources\AlertaSOSResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlertaSOS extends CreateRecord
{
    protected static string $resource = AlertaSOSResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['condominio_id'] = auth()->user()->departamento?->condominio_id;
        return $data;
    }

    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}