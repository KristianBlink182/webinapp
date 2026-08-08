<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoResource\Pages;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Gastos del Edificio';
    protected static ?string $pluralModelLabel = 'Gastos y Egresos del Edificio';
    protected static ?string $navigationIcon = 'heroicon-o-document-minus';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('condominio_id')
                ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id),

            Forms\Components\Section::make('1. Datos del Gasto / Egreso')
                ->schema([
                    Forms\Components\Select::make('concepto')
                        ->label('Categoría de Gasto')
                        ->options([
                            'Luz Areas Comunes'  => '💡 Luz de Áreas Comunes (Luz del Edificio)',
                            'Agua Areas Comunes' => '💧 Agua de Áreas Comunes (Agua del Edificio)',
                            'Limpieza'           => '🧹 Servicio de Limpieza y Productos',
                            'Vigilancia'         => '👮 Servicio de Vigilancia / Portería',
                            'Ascensor'           => '🛗 Mantenimiento de Ascensores',
                            'Jardineria'         => '🌱 Jardinería y Fumigación',
                            'Administracion'     => '💼 Honorarios de Administración',
                            'Otro'               => '⚙️ Otro Gasto (Especificar)',
                        ])
                        ->default('Luz Areas Comunes')
                        ->required()
                        ->reactive(),

                    Forms\Components\TextInput::make('concepto_detalle')
                        ->label('Especifique el Gasto')
                        ->placeholder('Ej: Reparación de bomba de agua, Pintura de fachada')
                        ->required(fn ($get) => $get('concepto') === 'Otro')
                        ->visible(fn ($get) => $get('concepto') === 'Otro'),

                    Forms\Components\TextInput::make('monto')
                        ->label('Monto del Gasto (S/)')
                        ->prefix('S/')
                        ->numeric()
                        ->required(),

                    Forms\Components\Select::make('mes')
                        ->label('Mes del Egreso')
                        ->options([
                            'Enero' => 'Enero', 'Febrero' => 'Febrero', 'Marzo' => 'Marzo', 'Abril' => 'Abril',
                            'Mayo' => 'Mayo', 'Junio' => 'Junio', 'Julio' => 'Julio', 'Agosto' => 'Agosto',
                            'Septiembre' => 'Septiembre', 'Octubre' => 'Octubre', 'Noviembre' => 'Noviembre', 'Diciembre' => 'Diciembre'
                        ])
                        ->default('Enero')
                        ->required(),

                    Forms\Components\TextInput::make('anio')
                        ->label('Año')
                        ->numeric()
                        ->default(date('Y'))
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('2. Comprobante de Respaldo y Auditoría')
                ->description('Suba la factura, boleta o recibo emitido por el proveedor para transparencia con la Junta.')
                ->schema([
                    Forms\Components\TextInput::make('numero_factura')
                        ->label('N° de Factura / Boleta / Recibo')
                        ->placeholder('Ej: F001-002931 / Recibo Luz #839213'),

                    Forms\Components\Select::make('proveedor_id')
                        ->label('Empresa / Proveedor')
                        ->relationship('proveedor', 'nombre_empresa')
                        ->searchable()
                        ->nullable(),

                    Forms\Components\DatePicker::make('fecha_factura')
                        ->label('Fecha del Comprobante')
                        ->default(now()),

                    Forms\Components\FileUpload::make('comprobante')
                        ->label('PDF o Foto de la Factura / Boleta')
                        ->disk('public')
                        ->directory('gastos-comprobantes')
                        ->columnSpanFull(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('concepto_detalle')
                    ->label('Detalle / Nota')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto Egreso')
                    ->money('PEN')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('mes')
                    ->label('Periodo')
                    ->formatStateUsing(fn ($record) => $record->mes . ' ' . $record->anio),

                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('N° Comprobante')
                    ->placeholder('S/N'),

                Tables\Columns\ImageColumn::make('comprobante')
                    ->label('Factura')
                    ->disk('public')
                    ->circular(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit'   => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}