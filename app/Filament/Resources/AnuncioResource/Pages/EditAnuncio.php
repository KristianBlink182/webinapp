<?php

namespace App\Filament\Resources\AnuncioResource\Pages;

use App\Filament\Resources\AnuncioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnuncio extends EditRecord
{
    protected static string $resource = AnuncioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
