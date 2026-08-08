<?php

namespace App\Filament\Porteria\Resources;

use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VisitaResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Control de Visitas';
    protected static ?string $pluralModelLabel = 'Control de Visitas e Ingresos';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registro de Ingreso de Visitante')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_visitante')
                            ->label('Nombre del Visitante')
                            ->required()
                            ->placeholder('Ej: Carlos Mendoza'),

                        Forms\Components\TextInput::make('dni_visitante')
                            ->label('DNI / Documento')
                            ->placeholder('72839102'),

                        Forms\Components\Select::make('departamento_id')
                            ->label('Departamento a Visitar')
                            ->relationship('departamento', 'numero')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('motivo')
                            ->label('Tipo de Visita')
                            ->options([
                                'Familiar' => 'Familiar / Amigo',
                                'Delivery' => 'Delivery / Repartidor',
                                'Tecnico'  => 'Técnico / Mantenimiento',
                                'Otro'     => 'Otro',
                            ])
                            ->default('Familiar'),

                        Forms\Components\Select::make('estado_visita')
                            ->label('Estado de la Visita')
                            ->options([
                                'Programado' => '⌛ Programado por Vecino',
                                'Dentro'     => '🟢 Dentro del Edificio',
                                'Retirado'   => '⚪ Retirado / Ya se retiró',
                            ])
                            ->default('Dentro')
                            ->required(),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->departamento?->condominio_id),

                        Forms\Components\Hidden::make('fecha_entrada')
                            ->default(fn () => now('America/Lima')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                // 🎯 FIX DE REGISTRADO POR CON ->state(...) EN FILAMENT V3
                Tables\Columns\TextColumn::make('origen')
                    ->label('Registrado Por')
                    ->state(function (Visita $record): string {
                        if ($record->estado_visita === 'Programado') {
                            return 'Vecino (Dpto ' . ($record->departamento?->numero ?? 'N/A') . ')';
                        }
                        return 'Portería';
                    })
                    ->badge()
                    ->color(fn ($record) => $record->estado_visita === 'Programado' ? 'purple' : 'success'),

                Tables\Columns\TextColumn::make('motivo')
                    ->label('Tipo')
                    ->placeholder('Familiar'),

                Tables\Columns\TextColumn::make('fecha_entrada')
                    ->label('Hora Ingreso')
                    ->dateTime('d/m/y h:i A')
                    ->timezone('America/Lima')
                    ->sortable(),

                // ⏱️ TIEMPO DE ESTADÍA
                Tables\Columns\TextColumn::make('fecha_salida')
                    ->label('Estadía')
                    ->formatStateUsing(function ($record) {
                        if (!$record->fecha_salida) {
                            return 'En Edificio';
                        }
                        $entrada = Carbon::parse($record->fecha_entrada ?? $record->created_at);
                        $salida = Carbon::parse($record->fecha_salida);
                        return $entrada->diffForHumans($salida, true);
                    })
                    ->color(fn ($record) => $record->fecha_salida ? 'gray' : 'warning'),

                Tables\Columns\TextColumn::make('estado_visita')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Programado' => 'Anunciado',
                        'Retirado'   => 'Retirado',
                        default      => 'Dentro',
                    })
                    ->color(fn (string $state = null): string => match ($state) {
                        'Programado' => 'info',
                        'Retirado'   => 'gray',
                        default      => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('confirmarIngreso')
                    ->label('🟢 Dar Ingreso')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn (Visita $record) => $record->estado_visita === 'Programado')
                    ->action(function (Visita $record) {
                        $record->update([
                            'estado_visita' => 'Dentro',
                            'fecha_entrada' => now('America/Lima'),
                        ]);
                    }),

                Tables\Actions\Action::make('marcarSalida')
                    ->label('⚪ Marcar Salida')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->button()
                    ->visible(fn (Visita $record) => $record->estado_visita === 'Dentro' || empty($record->estado_visita))
                    ->action(function (Visita $record) {
                        $record->update([
                            'estado_visita' => 'Retirado',
                            'fecha_salida'  => now('America/Lima'),
                        ]);
                    }),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => VisitaResource\Pages\ListVisitas::route('/'),
        ];
    }
}