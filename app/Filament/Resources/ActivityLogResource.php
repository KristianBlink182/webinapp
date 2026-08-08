<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationLabel = 'Historial de Acciones';
    protected static ?string $modelLabel = 'Registro de Actividad';
    protected static ?string $pluralModelLabel = 'Historial de Acciones';
    protected static ?string $navigationGroup = 'Configuración';

    // El Historial SOLO lo puede ver el Administrador
    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }

    // Bloqueamos la creación y edición manual por seguridad de auditoría
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                
                TextColumn::make('causer.name')
                    ->label('Usuario responsable')
                    ->placeholder('Sistema / Automático')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Acción realizada')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'CREÓ',
                        'updated' => 'MODIFICÓ',
                        'deleted' => 'ELIMINÓ',
                        default => $state,
                    }),

                TextColumn::make('subject_type')
                    ->label('Módulo afectado')
                    ->formatStateUsing(fn ($state) => match(basename($state)) {
                        'User' => 'Usuario',
                        'Pago' => 'Pago/Recibo',
                        'Gasto' => 'Gasto de Edificio',
                        'Condominio' => 'Condominio',
                        'Mascota' => 'Mascota',
                        default => basename($state),
                    }),

                TextColumn::make('properties')
                    ->label('Detalles técnicos')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver detalle completo'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}