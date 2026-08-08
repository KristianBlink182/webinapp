<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaqueteResource\Pages;
use App\Models\Paquete;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class PaqueteResource extends Resource
{
    protected static ?string $model = Paquete::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Paquetes y Correspondencia';
    protected static ?string $modelLabel = 'Paquete';
    protected static ?string $navigationGroup = 'Seguridad'; // Agrupado con Visitas
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'vigilante']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registro de Paquete')
                    ->schema([
                        Select::make('departamento_id')
                            ->label('Departamento Destino')
                            ->relationship('departamento', 'numero')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('destinatario')
                            ->label('Nombre del Destinatario')
                            ->required(),
                        TextInput::make('empresa_envio')
                            ->label('Empresa / Courier')
                            ->placeholder('Ej: Amazon, Mercado Libre, Rappi'),
                        TextInput::make('descripcion')
                            ->label('Descripción del bulto')
                            ->placeholder('Ej: Caja pequeña, sobre manila'),
                        DateTimePicker::make('fecha_recibido')
                            ->label('Fecha de Recepción')
                            ->default(now())
                            ->required(),
                        Select::make('estado')
                            ->options([
                                'En Recepción' => 'En Recepción',
                                'Entregado' => 'Entregado',
                            ])->default('En Recepción')->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('departamento.numero')
                    ->label('Dep.')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('destinatario')
                    ->label('Destinatario')
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En Recepción' => 'warning',
                        'Entregado' => 'success',
                    }),
                TextColumn::make('fecha_recibido')
                    ->label('Recibido')
                    ->dateTime('d/m H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'En Recepción' => 'En Recepción',
                        'Entregado' => 'Entregado',
                    ]),
            ])
            ->actions([
                // BOTÓN RÁPIDO: Entregar paquete
                Action::make('entregar')
                    ->label('Marcar Entregado')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Paquete $record) => $record->estado === 'En Recepción')
                    ->action(fn (Paquete $record) => $record->update([
                        'estado' => 'Entregado',
                        'fecha_entregado' => now()
                    ])),
                Tables\Actions\EditAction::make(),
            ]);
    }

    // El vecino solo ve sus propios paquetes
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role === 'residente') {
            $query->where('departamento_id', auth()->user()->departamento_id);
        }
        return $query;
    }

    public static function getPages(): array {
        return ['index' => Pages\ListPaquetes::route('/'), 'create' => Pages\CreatePaquete::route('/create'), 'edit' => Pages\EditPaquete::route('/{record}/edit')];
    }
}