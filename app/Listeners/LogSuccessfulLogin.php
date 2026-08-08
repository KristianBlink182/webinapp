<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\BitacoraAcceso;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        $condoNombre = $user->departamento?->condominio?->nombre 
            ?? $user->condominios->first()?->nombre 
            ?? 'Global';

        BitacoraAcceso::create([
            'user_id' => $user->id,
            'user_name' => $user->name . ' (' . ucfirst($user->role ?? 'Usuario') . ')',
            'condominio_nombre' => $condoNombre,
            'ip_address' => request()->ip(),
        ]);
    }
}