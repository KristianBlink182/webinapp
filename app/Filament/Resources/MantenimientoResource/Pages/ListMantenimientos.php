<?php

namespace App\Filament\Resources\MantenimientoResource\Pages;

use App\Filament\Resources\MantenimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListMantenimientos extends ListRecords
{
    protected static string $resource = MantenimientoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '⚙️',
            'badge'       => 'Equipos & Maquinarias',
            'title'       => 'Calendario de Mantenimiento Preventivo',
            'description' => 'Programación y registro de inspecciones técnicas de ascensores, bombas de agua, tableros y puertas eléctricas.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Mantenimiento')
                ->createAnother(false),
        ];
    }
}