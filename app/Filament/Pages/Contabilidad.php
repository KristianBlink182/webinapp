<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pago;
use App\Models\Gasto;

class Contabilidad extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Contabilidad y Libro Mayor';
    protected static ?string $navigationGroup = 'Contabilidad & Reportes';
    protected static ?string $title = 'Contabilidad de Caja y Libro Mayor';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.contabilidad';

    public function getViewData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

        $ingresos = Pago::whereHas('departamento', fn($q) => $q->where('condominio_id', $condoId))
            ->where('estado', 'Pagado')
            ->latest()
            ->get();

        $egresos = Gasto::where('condominio_id', $condoId)->latest()->get();

        $totalIngresos = $ingresos->sum('monto');
        $totalEgresos = $egresos->sum('monto');

        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'saldoCaja' => $totalIngresos - $totalEgresos,
        ];
    }
}