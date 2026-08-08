<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    protected $fillable = [
        'condominio_id',
        'departamento_id',
        'nombre_visitante',
        'dni_visitante',
        'motivo',
        'fecha_entrada',
        'fecha_salida',
        'estado_visita',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}