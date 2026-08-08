<?php

namespace App\Filament\Master\Pages;

use Filament\Pages\Page;
use App\Models\Condominio;
use App\Models\Pago;

class ReporteSaaS extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reportes SaaS';
    protected static ?string $title = 'Reportes y Analíticas Ejecutivas';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.master.pages.reporte-saas';

    public function getViewData(): array
    {
        $condominios = Condominio::withCount('departamentos')->get();
        $totalProcesado = Pago::where('estado', 'Aprobado')->sum('monto') ?? 0;
        $mrrSaaS = Condominio::where('estado_servicio', 'Activo')->sum('precio_mensual_saas') ?? 0;

        return [
            'condominios' => $condominios,
            'totalProcesado' => $totalProcesado,
            'mrrSaaS' => $mrrSaaS,
        ];
    }
}