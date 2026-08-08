<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Departamento;
use App\Models\Condominio;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateDepartamento extends CreateRecord
{
    protected static string $resource = DepartamentoResource::class;

    // 🎯 REDIRECCIÓN DIRECTA A LA TABLA DE DEPARTAMENTOS
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // 🎯 REMOVE EL BOTÓN "CREAR Y CREAR OTRO"
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function beforeCreate(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id;

        $condominio = Condominio::find($condoId);
        $plan = $condominio?->plan_saas ?? 'Básico';

        $limiteMaximo = match ($plan) {
            'Básico' => 20,
            'Pro' => 100,
            'Enterprise' => 999999,
            default => 20,
        };

        $actuales = Departamento::where('condominio_id', $condoId)->count();

        if ($actuales >= $limiteMaximo) {
            Notification::make()
                ->title('⛔ Límite del Plan Alcanzado')
                ->body("Ha alcanzado el límite máximo de {$limiteMaximo} departamentos permitido en su Plan {$plan}. Contacte a Soporte LIVO para ampliar la capacidad.")
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $data = $this->data;
        $email = $data['email_propietario'] ?? null;
        $password = $data['password_accesos'] ?? '123456';
        $nombre = $data['nombre_propietario'] ?? 'Residente';

        if (!empty($email)) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => Hash::make($password),
                    'role' => 'residente',
                    'departamento_id' => $this->record->id,
                ]
            );
        }
    }
}