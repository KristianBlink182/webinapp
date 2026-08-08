<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListPersonals extends ListRecords
{
    protected static string $resource = PersonalResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '👨‍✈️',
            'badge'       => 'Recursos Humanos',
            'title'       => 'Directorio de Personal & Vigilantes',
            'description' => 'Control de turnos, puestos de trabajo, teléfonos y accesos para el personal de seguridad y limpieza del condominio.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Empleado')
                ->createAnother(false),
        ];
    }
}