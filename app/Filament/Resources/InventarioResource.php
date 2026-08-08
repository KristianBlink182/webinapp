<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioResource\Pages;
use App\Models\Inventario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Inventario y Activos';
    protected static ?string $modelLabel = 'Activo / Suministro';
   protected static ?string $navigationGroup = 'Mantenimiento & Equipos';

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Artículo')
                    ->schema([
                        Select::make('condominio_id')
                            ->label('Condominio')
                            ->relationship('condominio', 'nombre')
                            ->required()
                            ->searchable(),
                        TextInput::make('nombre')
                            ->label('Nombre del objeto')
                            ->required()
                            ->placeholder('Ej: Escalera de aluminio'),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Select::make('unidad_medida')
                            ->label('Unidad')
                            ->options([
                                'unidades' => 'Unidades',
                                'litros' => 'Litros',
                                'galones' => 'Galones',
                                'paquetes' => 'Paquetes',
                            ])->default('unidades'),
                        Select::make('estado')
                            ->label('Estado actual')
                            ->options([
                                'Nuevo' => 'Nuevo',
                                'Bueno' => 'Bueno',
                                'Regular' => 'Regular',
                                'Mal Estado' => 'Mal Estado',
                            ])->required(),
                        TextInput::make('ubicacion')
                            ->label('¿Dónde se encuentra?')
                            ->placeholder('Ej: Sótano 2, Almacén de limpieza'),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Notas adicionales')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('condominio.nombre')->label('Condominio')->sortable(),
                TextColumn::make('nombre')->label('Artículo')->searchable(),
                TextColumn::make('cantidad')
                    ->label('Stock')
                    ->formatStateUsing(fn ($state, $record) => "{$state} {$record->unidad_medida}"),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo' => 'success',
                        'Bueno' => 'info',
                        'Regular' => 'warning',
                        'Mal Estado' => 'danger',
                    }),
                TextColumn::make('ubicacion')->label('Ubicación'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Nuevo' => 'Nuevo',
                        'Bueno' => 'Bueno',
                        'Regular' => 'Regular',
                        'Mal Estado' => 'Mal Estado',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
        ];
    }
}