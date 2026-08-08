<?php

namespace App\Filament\Resources\VotacionResource\Pages;

use App\Filament\Resources\VotacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVotacion extends EditRecord
{
    protected static string $resource = VotacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
