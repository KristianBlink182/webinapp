<?php

namespace App\Policies;

use App\Models\Anuncio;
use App\Models\User;

class AnuncioPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Anuncio $anuncio): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Anuncio $anuncio): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $user->id === $anuncio->user_id;
    }

    public function delete(User $user, Anuncio $anuncio): bool
    {
        return in_array($user->role, ['superadmin', 'admin']) || $user->id === $anuncio->user_id;
    }
}