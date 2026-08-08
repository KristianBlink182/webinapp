<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'concepto',
        'concepto_detalle',
        'descripcion',
        'monto',
        'mes',
        'anio',
        'fecha_gasto',
        'fecha_factura',
        'numero_factura',
        'comprobante',
        'proveedor_id',
    ];

    // 🎯 ASIGNA AUTOMÁTICAMENTE DESCRIPCIÓN Y FECHA PARA EVITAR NOT NULL EN SQLITE
    protected static function booted(): void
    {
        static::creating(function (Gasto $gasto) {
            if (empty($gasto->descripcion)) {
                $gasto->descripcion = $gasto->concepto ?? $gasto->concepto_detalle ?? 'Gasto de Administración';
            }
            if (empty($gasto->fecha_gasto)) {
                $gasto->fecha_gasto = $gasto->fecha_factura ?? now()->format('Y-m-d');
            }
        });

        static::updating(function (Gasto $gasto) {
            if (empty($gasto->descripcion)) {
                $gasto->descripcion = $gasto->concepto ?? $gasto->concepto_detalle ?? 'Gasto de Administración';
            }
            if (empty($gasto->fecha_gasto)) {
                $gasto->fecha_gasto = $gasto->fecha_factura ?? now()->format('Y-m-d');
            }
        });
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}