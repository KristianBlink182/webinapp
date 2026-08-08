<?php

namespace App\Filament\Resources\ReclamoResource\Pages;

use App\Filament\Resources\ReclamoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReclamo extends CreateRecord
{
    protected static string $resource = ReclamoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // El usuario logueado es el dueño del reclamo
        $data['user_id'] = auth()->id();
        
        // Si es residente, le asignamos su condominio automáticamente
        if (auth()->user()->isResidente()) {
            $data['condominio_id'] = auth()->user()->departamento->condominio_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}