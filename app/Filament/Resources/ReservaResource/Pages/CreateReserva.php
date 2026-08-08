<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Reserva;
use Filament\Notifications\Notification;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $existeCruce = Reserva::where('area_comun_id', $data['area_comun_id'])
            ->where('fecha', $data['fecha'])
            ->where('estado', '!=', 'Cancelada')
            ->where(function ($query) use ($data) {
                $query->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
                      ->orWhereBetween('hora_fin', [$data['hora_inicio'], $data['hora_fin']]);
            })->exists();

        if ($existeCruce) {
            Notification::make()->title('Horario no disponible')->danger()->send();
            $this->halt();
        }
        return $data;
    }
}