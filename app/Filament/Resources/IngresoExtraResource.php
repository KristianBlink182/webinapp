<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngresoExtraResource\Pages;
use App\Models\IngresoExtra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class IngresoExtraResource extends Resource
{
    protected static ?string $model = IngresoExtra::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Multas e Ingresos Extra';
    protected static ?string $modelLabel = 'Ingreso Extra / Multa';
    protected static ?string $navigationGroup = 'Finanzas'; // Agrupado con Pagos y Gastos
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalle del Ingreso')
                    ->schema([
                        Select::make('condominio_id')
                            ->label('Condominio')
                            ->relationship('condominio', 'nombre')
                            ->required()
                            ->reactive(),
                        Select::make('departamento_id')
                            ->label('Departamento (Opcional)')
                            ->relationship('departamento', 'numero', fn ($query, $get) => 
                                $query->where('condominio_id', $get('condominio_id'))
                            )
                            ->helperText('Deje vacío si es un ingreso general del edificio'),
                        TextInput::make('titulo')
                            ->label('Concepto / Título')
                            ->required()
                            ->placeholder('Ej: Multa por desacato de normas'),
                        Select::make('categoria')
                            ->label('Categoría')
                            ->options([
                                'Multa' => 'Multa',
                                'Alquiler' => 'Alquiler',
                                'Donación' => 'Donación',
                                'Otro' => 'Otro',
                            ])->required(),
                        TextInput::make('monto')
                            ->label('Monto S/')
                            ->numeric()
                            ->prefix('S/')
                            ->required(),
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'Pagado' => 'Pagado',
                            ])->default('Pendiente')->required(),
                        DatePicker::make('fecha_registro')
                            ->label('Fecha')
                            ->default(now())
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('condominio.nombre')->label('Condominio'),
                TextColumn::make('departamento.numero')->label('Dep.')->placeholder('General'),
                TextColumn::make('titulo')->label('Concepto')->searchable(),
                TextColumn::make('categoria')->badge(),
                TextColumn::make('monto')->label('Monto')->money('PEN'),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'danger',
                        'Pagado' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')->options([
                    'Multa' => 'Multa',
                    'Alquiler' => 'Alquiler',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListIngresoExtras::route('/'),
            'create' => Pages\CreateIngresoExtra::route('/create'),
            'edit' => Pages\EditIngresoExtra::route('/{record}/edit'),
        ];
    }
}