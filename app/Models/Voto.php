<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    protected $fillable = [
        'votacion_id', 
        'user_id', 
        'opcion_seleccionada'
    ];

    public function votacion(): BelongsTo
    {
        return $this->belongsTo(Votacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}