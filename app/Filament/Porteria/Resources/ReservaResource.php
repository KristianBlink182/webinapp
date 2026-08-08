<?php

namespace App\Filament\Porteria\Resources;

use App\Models\Reserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Agenda Áreas Comunes';
    protected static ?string $pluralModelLabel = 'Reservas del Edificio';
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areaComun.nombre')
                    ->label('Área Común')
                    ->weight('bold')
                    ->color('primary'),

                // 🎯 FIX: MUESTRA EL NOMBRE Y DEPARTAMENTO QUE RESERVÓ
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dpto Reservante')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => ($record->user?->name ?? 'Residente') . ' (Dpto. ' . ($record->departamento?->numero ?? $record->user?->departamento?->numero ?? 'N/A') . ')'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Horario')
                    ->formatStateUsing(fn ($record) => date('h:i A', strtotime($record->hora_inicio)) . ' - ' . date('h:i A', strtotime($record->hora_fin))),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Aprobada'  => 'success',
                        'Pendiente' => 'warning',
                        'Cancelada' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ReservaResource\Pages\ListReservas::route('/'),
        ];
    }
}