<?php

namespace App\Imports;

use App\Models\Pago;
use App\Models\Departamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConsumoAguaImport implements ToModel, WithHeadingRow
{
    protected $mes;
    protected $anio;
    protected $costo_m3;

    public function __construct($mes, $anio, $costo_m3)
    {
        $this->mes = $mes;
        $this->anio = $anio;
        $this->costo_m3 = $costo_m3;
    }

    public function model(array $row)
    {
        // 1. Buscamos el departamento por número
        $dep = Departamento::where('numero', $row['numero'])->first();

        if ($dep) {
            // 2. Buscamos el recibo de ese mes (que generamos masivamente antes)
            $pago = Pago::where('departamento_id', $dep->id)
                ->where('mes', $this->mes)
                ->where('anio', $this->anio)
                ->first();

            if ($pago) {
                $lecturaAnterior = $pago->lectura_anterior ?? 0;
                $lecturaActual = $row['lectura_actual'];
                $consumo = $lecturaActual - $lecturaAnterior;
                $montoAgua = $consumo * $this->costo_m3;

                // 3. Actualizamos el recibo con la nueva lectura y monto
                $pago->update([
                    'lectura_actual' => $lecturaActual,
                    'monto_agua' => $montoAgua,
                    // Sumamos el total: mantenimiento + agua + luz (si hubiera)
                    'monto' => $pago->monto_mantenimiento + $montoAgua + ($pago->monto_luz ?? 0)
                ]);
            }
        }
        return null;
    }
}