<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banco extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'nombre_banco',
        'numero_cuenta',
        'tipo_cuenta',
        'saldo_inicial'
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }
}