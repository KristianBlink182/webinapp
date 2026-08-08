<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personal extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'nombre_completo',
        'nombre',
        'dni',
        'puesto',
        'cargo',
        'telefono',
        'email',
        'turno',
        'estado',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }
}