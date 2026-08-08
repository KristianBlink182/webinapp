<?php

namespace App\Filament\Porteria\Resources\VisitaResource\Pages;

use App\Filament\Porteria\Resources\VisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Visita;

class ListVisitas extends ListRecords
{
    protected static string $resource = VisitaResource::class;
    protected static ?string $title = 'Control de Visitas e Ingresos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Nueva Visita')
                ->createAnother(false), // 🎯 QUITA "CREAR Y CREAR OTRO" Y VA DIRECTO A LA TABLA
        ];
    }

    public function getTabs(): array
    {
        return [
            'historial' => Tab::make('Historial Completo')
                ->icon('heroicon-m-list-bullet')
                ->badge(Visita::count()),

            'programados' => Tab::make('Invitados Anunciados por Vecinos')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado_visita', 'Programado'))
                ->badge(Visita::where('estado_visita', 'Programado')->count()),

            'dentro' => Tab::make('Visitas Dentro del Edificio')
                ->icon('heroicon-m-user-group')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado_visita', ['Dentro', null, '']))
                ->badge(Visita::whereIn('estado_visita', ['Dentro', null, ''])->count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'historial';
    }
}