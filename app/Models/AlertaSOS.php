<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaSOS extends Model
{
    use HasFactory;

    protected $table = 'alerta_s_o_s';

    protected $fillable = [
        'condominio_id',
        'departamento_id',
        'user_id',
        'tipo',
        'descripcion',
        'audio_path',
        'estado',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}