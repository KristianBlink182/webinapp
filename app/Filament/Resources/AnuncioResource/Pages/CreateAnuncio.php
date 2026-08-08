<?php

namespace App\Filament\Resources\AnuncioResource\Pages;

use App\Filament\Resources\AnuncioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnuncio extends CreateRecord
{
    protected static string $resource = AnuncioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // El usuario logueado es el vendedor
        $data['user_id'] = auth()->id();

        // Se asigna automáticamente al condominio donde vive el vecino
        $data['condominio_id'] = auth()->user()->departamento?->condominio_id ?? 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // 🎯 REMUEVE EL BOTÓN "CREAR Y CREAR OTRO"
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}