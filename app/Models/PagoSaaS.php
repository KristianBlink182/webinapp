<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoSaaS extends Model
{
    protected $table = 'pago_saas';

   protected $fillable = [
    'condominio_id',
    'plan',
    'monto',
    'monto_base',
    'monto_igv',
    'monto_total',
    'tipo_comprobante',
    'dni',
    'nombre',
    'ruc',
    'razon_social',
    'direccion_fiscal',
    'voucher',
    'estado',
    'comprobante_factura',
];

    public function condominio()
    {
        return $this->belongsTo(Condominio::class, 'condominio_id');
    }
}