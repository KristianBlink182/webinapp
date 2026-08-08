<?php

namespace App\Filament\Resources\ComunicadoResource\Pages;

use App\Filament\Resources\ComunicadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComunicado extends EditRecord
{
    protected static string $resource = ComunicadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
