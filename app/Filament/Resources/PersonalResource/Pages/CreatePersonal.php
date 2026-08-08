<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonal extends CreateRecord
{
    protected static string $resource = PersonalResource::class;

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

    protected function afterCreate(): void
    {
        $data = $this->data;
        $email = $data['email'] ?? null;
        $password = $data['password_acceso'] ?? '123456';
        $nombre = $data['nombre_completo'] ?? 'Vigilante';
        $puesto = $data['puesto'] ?? 'Vigilante';

        if (!empty($email) && $puesto === 'Vigilante') {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'role' => 'vigilante',
                ]
            );

            $condoId = \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id;
            if ($condoId) {
                $user->condominios()->syncWithoutDetaching([$condoId]);
            }
        }
    }
}