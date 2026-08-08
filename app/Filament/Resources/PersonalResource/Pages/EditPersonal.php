<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EditPersonal extends EditRecord
{
    protected static string $resource = PersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->data;
        $email = $data['email'] ?? null;
        $password = $data['password_acceso'] ?? null;
        $nombre = $data['nombre_completo'] ?? 'Vigilante';
        $puesto = $data['puesto'] ?? 'Vigilante';

        if (!empty($email) && $puesto === 'Vigilante') {
            $user = User::where('email', $email)->first();

            if ($user) {
                $userData = [
                    'name' => $nombre,
                    'role' => 'vigilante',
                ];

                if (!empty($password)) {
                    $userData['password'] = Hash::make($password);
                }

                $user->update($userData);
            } else {
                $user = User::create([
                    'name' => $nombre,
                    'email' => $email,
                    'password' => Hash::make(!empty($password) ? $password : '123456'),
                    'role' => 'vigilante',
                ]);
            }

            $condoId = \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id;
            if ($condoId) {
                $user->condominios()->syncWithoutDetaching([$condoId]);
            }
        }
    }
}