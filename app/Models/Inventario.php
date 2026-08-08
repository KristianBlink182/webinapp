<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model {
    protected $fillable = ['condominio_id', 'nombre', 'descripcion', 'cantidad', 'unidad_medida', 'estado', 'ubicacion'];

    public function condominio(): BelongsTo {
        return $this->belongsTo(Condominio::class);
    }
}