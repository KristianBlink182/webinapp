<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Reserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Aprobación de Reservas';
    protected static ?string $pluralModelLabel = 'Reservas de Áreas Comunes';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areaComun.nombre')
                    ->label('Área Común')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reservado Por')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => $record->user?->name . ' (Dpto. ' . ($record->departamento?->numero ?? $record->user?->departamento?->numero ?? 'N/A') . ')'),

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
            ->defaultSort('fecha', 'desc')
            ->actions([
                // 🔍 BOTÓN PARA VER EL VOUCHER EN TAMAÑO COMPLETO EN POP-UP
                Tables\Actions\Action::make('verVoucher')
                    ->label('Ver Voucher')
                    ->icon('heroicon-m-photo')
                    ->color('info')
                    ->button()
                    ->visible(fn (Reserva $record) => !empty($record->voucher))
                    ->modalHeading('Comprobante de Pago de Reserva')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (Reserva $record) => new HtmlString('
                        <div style="text-align: center; padding: 1rem;">
                            <img src="' . asset('storage/' . $record->voucher) . '" style="max-width: 100%; height: auto; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.5); margin: auto;">
                        </div>
                    ')),

                // 🟢 BOTÓN APROBAR
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->button()
                    ->visible(fn (Reserva $record) => $record->estado === 'Pendiente')
                    ->action(fn (Reserva $record) => $record->update(['estado' => 'Aprobada'])),

                // 🔴 BOTÓN RECHAZAR
                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->button()
                    ->visible(fn (Reserva $record) => $record->estado === 'Pendiente')
                    ->action(fn (Reserva $record) => $record->update(['estado' => 'Cancelada'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservas::route('/'),
        ];
    }
}