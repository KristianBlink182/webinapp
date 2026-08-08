<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model {
    protected $fillable = ['condominio_id', 'equipo', 'ultima_fecha', 'proxima_fecha', 'proveedor', 'notas'];

    public function condominio(): BelongsTo {
        return $this->belongsTo(Condominio::class);
    }
}