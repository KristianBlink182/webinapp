<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'numero',
        'nombre',
        'piso',
        'porcentaje_participacion',
        'nombre_propietario',
        'email_propietario',
        'telefono_propietario',
        'condicion',
        'nombre_inquilino',
        'telefono_inquilino',
        'email_inquilino',
        'estacionamiento',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}