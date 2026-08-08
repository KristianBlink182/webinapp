<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visita;

class VisitaPolicy
{
    /**
     * ¿Quién puede ver la lista de visitas?
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ¿Quién puede registrar una visita nueva?
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ¿Quién puede editar una visita?
     */
    public function update(User $user, Visita $visita): bool
    {
        $role = strtolower($user->role ?? '');
        $isAdmin = in_array($role, ['admin', 'administrador', 'super_admin', 'master']);
        $isVigilante = in_array($role, ['porteria', 'vigilante', 'portero']);

        if ($isAdmin || $isVigilante) {
            return true;
        }

        // El residente solo edita si es su propia visita programada
        return $user->departamento_id === $visita->departamento_id;
    }

    /**
     * ¿Quién puede borrar una visita?
     */
    public function delete(User $user, Visita $visita): bool
    {
        $role = strtolower($user->role ?? '');
        $isAdmin = in_array($role, ['admin', 'administrador', 'super_admin', 'master']);

        if ($isAdmin) {
            return true;
        }

        // El residente también puede cancelar su propia invitación
        return $user->departamento_id === $visita->departamento_id;
    }
}