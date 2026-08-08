<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model {
    protected $fillable = [
        'departamento_id', 'mes', 'anio', 'monto_mantenimiento', 
        'monto_luz', 'monto_agua', 'saldo_anterior', 'monto_mora', 
        'monto', 'estado', 'fecha_pago', 'voucher', 'nota_administrador',
        'lectura_anterior', 'lectura_actual', 'foto_medidor'
    ];

    public function departamento(): BelongsTo {
        return $this->belongsTo(Departamento::class);
    }
}