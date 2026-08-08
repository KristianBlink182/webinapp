<?php

namespace App\Filament\Resources\AlertaSOSResource\Pages;

use App\Filament\Resources\AlertaSOSResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListAlertaSOS extends ListRecords
{
    protected static string $resource = AlertaSOSResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🚨',
            'badge'       => 'Seguridad del Edificio',
            'title'       => 'Monitoreo de Emergencias & Alertas S.O.S.',
            'description' => 'Monitoreo en tiempo real de llamadas de auxilio emitidas por los residentes.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('🆘 PEDIR AYUDA (SOS)')
                ->color('danger')
                ->createAnother(false),
        ];
    }
}