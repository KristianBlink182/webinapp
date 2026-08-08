<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pago $pago): bool
    {
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }

        return $user->role === 'residente' && $user->departamento_id === $pago->departamento_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function update(User $user, Pago $pago): bool
    {
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }

        return $user->role === 'residente' && 
               $user->departamento_id === $pago->departamento_id && 
               strtolower($pago->estado) === 'pendiente';
    }

    public function delete(User $user, Pago $pago): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}