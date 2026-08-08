<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IngresoExtra extends Model
{
    use HasFactory;

    // Esto obliga a Laravel a buscar exactamente esta tabla
    protected $table = 'ingreso_extras';

    protected $fillable = [
        'condominio_id', 
        'departamento_id', 
        'titulo', 
        'categoria', 
        'monto', 
        'estado', 
        'fecha_registro'
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