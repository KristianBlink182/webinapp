<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Condominio;
use App\Models\Pago;
use App\Models\Gasto;
use App\Models\Comunicado;

/*
|--------------------------------------------------------------------------
| 💾 PROGRAMADOR DE RESPALDOS AUTOMÁTICOS ROTATIVOS LIVO (12:00 AM)
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $condominios = Condominio::all();

    foreach ($condominios as $condo) {
        $datos = [
            'condominio'     => $condo,
            'departamentos'  => $condo->departamentos,
            'pagos'          => Pago::where('condominio_id', $condo->id)->get(),
            'gastos'         => Gasto::where('condominio_id', $condo->id)->get(),
            'comunicados'    => Comunicado::where('condominio_id', $condo->id)->get(),
            'fecha_respaldo' => now('America/Lima')->toDateTimeString(),
        ];

        $json = json_encode($datos, JSON_PRETTY_PRINT);
        
        // Carpeta privada por condominio
        $folder = storage_path('app/backups_automaticos/' . str($condo->nombre)->slug());

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        // Nombre con el día de la semana (ej: backup-lunes.json)
        $dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diaHoy = $dias[date('w')];
        
        $filename = $folder . '/backup-' . $diaHoy . '.json';

        // Guarda y sobreescribe automáticamente cada semana
        file_put_contents($filename, $json);
    }
})->dailyAt('00:00');