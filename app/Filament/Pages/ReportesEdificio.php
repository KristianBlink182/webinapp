<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pago;
use App\Models\Gasto;
use App\Models\Departamento;
use App\Models\Visita;

class ReportesEdificio extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Centro de Reportes';
    protected static ?string $navigationGroup = 'Contabilidad & Reportes';
    protected static ?string $title = 'Generador de Reportes y Filtros';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.reportes-edificio';

    public $tipo_reporte = 'morosidad';
    public $fecha_inicio;
    public $fecha_fin;
    public $departamento_id;
    public $buscar_dni;

    public function mount(): void
    {
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
    }

    public function getViewData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        $departamentos = Departamento::where('condominio_id', $condoId)->get();
        $resultados = [];

        if ($this->tipo_reporte === 'morosidad') {
            $query = Pago::whereHas('departamento', fn($q) => $q->where('condominio_id', $condoId))
                ->where('estado', 'Pendiente');

            if ($this->departamento_id) {
                $query->where('departamento_id', $this->departamento_id);
            }

            $resultados = $query->with('departamento')->latest()->get();

        } elseif ($this->tipo_reporte === 'ingresos') {
            $query = Pago::whereHas('departamento', fn($q) => $q->where('condominio_id', $condoId))
                ->where('estado', 'Pagado');

            if ($this->departamento_id) {
                $query->where('departamento_id', $this->departamento_id);
            }

            $resultados = $query->with('departamento')->latest()->get();

        } elseif ($this->tipo_reporte === 'gastos') {
            $query = Gasto::where('condominio_id', $condoId);

            if ($this->fecha_inicio && $this->fecha_fin) {
                $query->whereBetween('created_at', [$this->fecha_inicio . ' 00:00:00', $this->fecha_fin . ' 23:59:59']);
            }

            $resultados = $query->latest()->get();

        } elseif ($this->tipo_reporte === 'visitas') {
            $query = Visita::whereHas('departamento', fn($q) => $q->where('condominio_id', $condoId));

            if ($this->buscar_dni) {
                $query->where('dni_visitante', 'like', "%{$this->buscar_dni}%");
            }

            $resultados = $query->with('departamento')->latest()->get();
        }

        return [
            'departamentos' => $departamentos,
            'resultados'    => $resultados,
        ];
    }

    // 📊 EXPORTAR REPORTE A EXCEL COMPATIBLE (.CSV BOM UTF-8)
    public function exportarExcel()
    {
        $viewData = $this->getViewData();
        $resultados = $viewData['resultados'] ?? [];
        $tipo = $this->tipo_reporte;

        $filename = 'Reporte-LIVO-' . ucfirst($tipo) . '-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $bom = "\xEF\xBB\xBF";
        $csv = $bom;

        if ($tipo === 'morosidad' || $tipo === 'ingresos') {
            $csv .= "Concepto,Departamento,Fecha,Monto Soles,Estado\n";
            foreach ($resultados as $item) {
                $concepto = $item->concepto ?? 'Cuota de Mantenimiento';
                $dpto = $item->departamento?->numero ?? 'N/A';
                $fecha = $item->created_at ? $item->created_at->format('d/m/Y h:i A') : 'N/A';
                $monto = number_format($item->monto ?? 0, 2, '.', '');
                $estado = $item->estado ?? 'Pendiente';
                $csv .= "\"{$concepto}\",\"Dpto {$dpto}\",\"{$fecha}\",\"{$monto}\",\"{$estado}\"\n";
            }
        } elseif ($tipo === 'gastos') {
            $csv .= "Concepto,Monto Soles,Fecha Factura,Numero Factura\n";
            foreach ($resultados as $item) {
                $concepto = $item->concepto ?? $item->descripcion ?? 'Gasto del Edificio';
                $monto = number_format($item->monto ?? 0, 2, '.', '');
                $fecha = $item->fecha_factura ?? $item->fecha_gasto ?? ($item->created_at ? $item->created_at->format('d/m/Y') : 'N/A');
                $factura = $item->numero_factura ?? 'N/A';
                $csv .= "\"{$concepto}\",\"{$monto}\",\"{$fecha}\",\"{$factura}\"\n";
            }
        } else {
            $csv .= "Visitante,DNI,Departamento,Fecha y Hora,Estado\n";
            foreach ($resultados as $item) {
                $visitante = $item->nombre_visitante ?? 'Visitante';
                $dni = $item->dni_visitante ?? 'N/A';
                $dpto = $item->departamento?->numero ?? 'N/A';
                $fecha = $item->created_at ? $item->created_at->format('d/m/Y h:i A') : 'N/A';
                $estado = $item->estado_visita ?? $item->estado ?? 'Registrado';
                $csv .= "\"{$visitante}\",\"{$dni}\",\"Dpto {$dpto}\",\"{$fecha}\",\"{$estado}\"\n";
            }
        }

        return response($csv, 200, $headers);
    }
}