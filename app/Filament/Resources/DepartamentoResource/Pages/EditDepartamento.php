<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EditDepartamento extends EditRecord
{
    protected static string $resource = DepartamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->data;
        $email = $data['email_propietario'] ?? null;
        $password = $data['password_acceso'] ?? null;
        $nombre = $data['nombre_propietario'] ?? 'Residente';

        if (!empty($email)) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $userData = [
                    'name' => $nombre,
                    'role' => 'residente',
                    'departamento_id' => $this->record->id,
                ];

                if (!empty($password)) {
                    $userData['password'] = Hash::make($password);
                }

                $user->update($userData);
            } else {
                User::create([
                    'name' => $nombre,
                    'email' => $email,
                    'password' => Hash::make(!empty($password) ? $password : '123456'),
                    'role' => 'residente',
                    'departamento_id' => $this->record->id,
                ]);
            }
        }
    }
}