<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paquete extends Model
{
    protected $fillable = [
        'departamento_id',
        'destinatario',
        'empresa_envio',
        'descripcion',
        'foto',
        'fecha_recibido',
        'fecha_entregado',
        'estado',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}