<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'departamento_id',
    'must_change_password',
    'telefono',
];
    protected $hidden = ['password', 'remember_token'];

    /**
     * ACCESO A PANELES
     */
   public function canAccessPanel(Panel $panel): bool
    {
        $role = strtolower($this->role ?? '');

        if ($role === 'super_admin' || $role === 'master') {
            return true;
        }

        return match ($panel->getId()) {
            'master'   => in_array($role, ['super_admin', 'master']),
            'admin'    => in_array($role, ['super_admin', 'master', 'admin', 'administrador']),
            'porteria' => in_array($role, ['super_admin', 'master', 'admin', 'vigilante', 'porteria']),
            'vecino'   => in_array($role, ['residente', 'admin', 'master', 'super_admin']),
            default    => false,
        };
    }

    /**
     * MULTI-TENANCY (TENANTS)
     */
    public function getTenants(Panel $panel): Collection
    {
        // 1. SI SOY SUPERADMIN, TENGO ACCESO A TODOS LOS EDIFICIOS
        if ($this->role === 'superadmin') {
            return Condominio::all();
        }

        // 2. SI EL USUARIO TIENE CONDOMINIOS ASIGNADOS DIRECTAMENTE (Ej: Admins / Vigilantes)
        if ($this->condominios->isNotEmpty()) {
            return $this->condominios;
        }

        // 3. SI ES RESIDENTE, SU CONDOMINIO VIENE A TRAVÉS DE SU DEPARTAMENTO
        if ($this->departamento?->condominio) {
            return collect([$this->departamento->condominio]);
        }

        return collect([]);
    }

  public function canAccessTenant(Model $tenant): bool
    {
        $role = strtolower($this->role ?? '');

        // 1. El Superadmin / Master siempre puede ingresar en Modo Fantasma a cualquier edificio
        if (in_array($role, ['super_admin', 'superadmin', 'master'])) {
            return true;
        }

        // 2. KILL SWITCH: Si el edificio está suspendido por falta de pago del SaaS, BLOQUEAR ACCESO
        if ($tenant->estado_servicio === 'Suspendido') {
            return false;
        }

        if ($this->condominios->contains($tenant)) return true;

        return $this->departamento?->condominio_id === $tenant->id;
    }

    public function condominios(): BelongsToMany
    {
        return $this->belongsToMany(Condominio::class, 'condominio_user');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
}