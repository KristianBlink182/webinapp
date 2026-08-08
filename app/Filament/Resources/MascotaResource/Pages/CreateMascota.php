<?php

namespace App\Filament\Resources\MascotaResource\Pages;

use App\Filament\Resources\MascotaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMascota extends CreateRecord
{
    protected static string $resource = MascotaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si el usuario es un residente, le asignamos su propio departamento automáticamente
        if (auth()->user()->role === 'residente') {
            $data['departamento_id'] = auth()->user()->departamento_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}