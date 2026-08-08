<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaComun extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'nombre',
        'costo',
        'mesas',
        'capacidad_max',
        'estado',
        'reglas',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }
}