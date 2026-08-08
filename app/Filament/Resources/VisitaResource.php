<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaResource\Pages;
use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitaResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Control de Visitas';
    protected static ?string $pluralModelLabel = 'Supervisión de Visitas e Ingresos';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $role = strtolower(auth()->user()->role ?? '');
        return in_array($role, ['admin', 'administrador', 'super_admin', 'master']);
    }

    // ⛔ MODO SUPERVISIÓN PURA: EL ADMIN NO CREA, EDITA NI BORRA REGISTROS DE PORTERÍA
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Supervisión en tiempo real de todos los ingresos registrados por la Portería y pre-registros de los vecinos.')
            ->columns([
                Tables\Columns\TextColumn::make('nombre_visitante')
                    ->label('Visitante')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dni_visitante')
                    ->label('DNI')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('departamento.numero')
                    ->label('Dpto Visitado')
                    ->badge()
                    ->color('info'),

                // 🎯 ORIGEN: INDICA SI LO REGISTRÓ EL VECINO O LA PORTERÍA
                Tables\Columns\TextColumn::make('origen')
                    ->label('Registrado Por')
                    ->state(function (Visita $record): string {
                        if ($record->estado_visita === 'Programado' || $record->estado_visita === 'Programada') {
                            return 'Vecino (Dpto ' . ($record->departamento?->numero ?? 'N/A') . ')';
                        }
                        return 'Portería';
                    })
                    ->badge()
                    ->color(fn ($record) => in_array($record->estado_visita, ['Programado', 'Programada']) ? 'purple' : 'success'),

                Tables\Columns\TextColumn::make('motivo')
                    ->label('Tipo')
                    ->placeholder('Familiar'),

                Tables\Columns\TextColumn::make('fecha_entrada')
                    ->label('Hora Entrada')
                    ->dateTime('d/m/y h:i A')
                    ->timezone('America/Lima')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_salida')
                    ->label('Hora Salida')
                    ->dateTime('d/m/y h:i A')
                    ->timezone('America/Lima')
                    ->placeholder('En Edificio'),

                Tables\Columns\TextColumn::make('estado_visita')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Programada', 'Programado' => 'Anunciado',
                        'Finalizada', 'Retirado', 'Salió' => 'Retirado',
                        default => 'En Edificio',
                    })
                    ->color(fn (string $state = null): string => match ($state) {
                        'Programada', 'Programado' => 'info',
                        'Ingresada', 'Dentro'      => 'success',
                        'Finalizada', 'Retirado', 'Salió' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado_visita')
                    ->label('Filtrar por')
                    ->options([
                        'Programado' => 'Invitaciones pendientes',
                        'Dentro'     => 'Personas adentro',
                        'Retirado'   => 'Ya se retiraron',
                    ]),
            ])
            ->actions([]); // 🎯 TABLA LIMPICITA SIN BOTONES INNECESARIOS
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $query = parent::getEloquentQuery();

        if ($tenant) {
            $query->where('condominio_id', $tenant->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitas::route('/'),
        ];
    }
}