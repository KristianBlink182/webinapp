<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicado extends Model
{
    protected $fillable = [
        'condominio_id',
        'titulo',
        'contenido',
        'tipo',
        'imagen_adjunto',
        'fecha_publicacion',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }
}