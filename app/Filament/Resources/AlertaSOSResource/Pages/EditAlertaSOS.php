<?php

namespace App\Filament\Resources\AlertaSOSResource\Pages;

use App\Filament\Resources\AlertaSOSResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlertaSOS extends EditRecord
{
    protected static string $resource = AlertaSOSResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
