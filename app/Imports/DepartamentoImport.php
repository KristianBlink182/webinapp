<?php

namespace App\Imports;

use App\Models\Departamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepartamentoImport implements ToModel, WithHeadingRow
{
    protected $condominio_id;

    public function __construct($condominio_id)
    {
        $this->condominio_id = $condominio_id;
    }

    public function model(array $row)
    {
        return new Departamento([
            'condominio_id' => $this->condominio_id,
            'numero'        => $row['numero'], // El Excel debe tener una columna llamada "numero"
            'piso'          => $row['piso'] ?? 1,
            'nombre_propietario' => $row['propietario'] ?? null,
            'dni_propietario'    => $row['dni'] ?? null,
            'email_propietario'  => $row['email'] ?? null,
            'porcentaje_participacion' => $row['participacion'] ?? 0,
        ]);
    }
}