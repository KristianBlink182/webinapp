<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenTrabajoResource\Pages;
use App\Models\OrdenTrabajo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrdenTrabajoResource extends Resource
{
    protected static ?string $model = OrdenTrabajo::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Mantenimiento & Equipos';
    protected static ?string $navigationLabel = 'Órdenes de Trabajo';
    protected static ?string $pluralModelLabel = 'Órdenes de Trabajo del Edificio';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('condominio_id')
                ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id),

            Forms\Components\Section::make('Mantenimiento / Reparación')
                ->schema([
                    Forms\Components\Select::make('proveedor_id')
                        ->label('Proveedor / Empresa Contratista')
                        ->relationship('proveedor', 'nombre_empresa')
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TextInput::make('titulo')
                        ->label('Tarea / Trabajo a Realizar')
                        ->required()
                        ->placeholder('Ej: Mantenimiento preventivo de ascensores'),

                    Forms\Components\DatePicker::make('fecha_programada')
                        ->label('Fecha Programada')
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado de la Orden')
                        ->options([
                            'Pendiente' => '🔴 Pendiente',
                            'En Proceso' => '🟡 En Proceso',
                            'Finalizado' => '🟢 Finalizado',
                        ])
                        ->default('Pendiente')
                        ->required(),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Detalles Técnicos y Observaciones')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Tarea')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('proveedor.nombre_empresa')
                    ->label('Proveedor')
                    ->placeholder('Sin asignar'),

                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('Fecha Programada')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Finalizado' => 'success',
                        'En Proceso' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenTrabajos::route('/'),
            'create' => Pages\CreateOrdenTrabajo::route('/create'),
            'edit' => Pages\EditOrdenTrabajo::route('/{record}/edit'),
        ];
    }
}