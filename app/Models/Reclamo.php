<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'condominio_id',
        'titulo',
        'descripcion',
        'foto',
        'prioridad',
        'estado',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}