<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $fillable = [
        'user_id',
        'departamento_id',
        'area_comun_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'cantidad_personas',
        'numero_mesa',
        'voucher',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function areaComun(): BelongsTo
    {
        return $this->belongsTo(AreaComun::class);
    }
}