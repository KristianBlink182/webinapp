<?php

namespace App\Filament\Resources\IngresoExtraResource\Pages;

use App\Filament\Resources\IngresoExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIngresoExtra extends EditRecord
{
    protected static string $resource = IngresoExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
