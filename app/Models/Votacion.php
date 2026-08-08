<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Votacion extends Model
{
    use HasFactory;

    protected $table = 'votacions';

    protected $fillable = [
        'condominio_id',
        'titulo',
        'descripcion',
        'documento_adjunto',
        'opciones',
        'fecha_limite',
        'esta_activa',
    ];

    protected $casts = [
        'opciones'     => 'array',
        'fecha_limite' => 'date',
        'esta_activa'  => 'boolean',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }
}