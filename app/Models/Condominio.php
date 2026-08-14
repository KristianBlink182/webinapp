<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\HasName;

class Condominio extends Model implements HasName
{
    use HasFactory;

   protected $fillable = [
    'nombre',
    'ruc',
    'direccion',
    'logo',
    'logo_claro',
    'url_camara_principal',
        'acepta_yape',
        'yape_numero',
        'yape_qr',
        'acepta_transferencia',
        'instrucciones_banco',
        'acepta_tarjeta',
        'pasarela_llave',
        'plan_saas',
        'precio_mensual_saas',
        'estado_servicio',
        'fecha_vencimiento_saas',
        'voucher_saas',
        'estado_pago_saas',
        'comprobante_factura_saas',
        'sismo_activo',
        'url_camara_principal',
        'tipo_comprobante_default',
'dni_default',
'nombre_default',
'ruc_default',
'razon_social_default',
'direccion_fiscal_default',
    ];

    public function getFilamentName(): string
    {
        return (string) ($this->nombre ?? 'Condominio LIVO');
    }

    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }
    /** Relación con las cuentas bancarias del edificio */
    public function bancos()
    {
        return $this->hasMany(Banco::class);
    }
}