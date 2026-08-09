<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Cobros y Recibos';
    protected static ?string $pluralModelLabel = 'Gestión de Cobros y Recibos';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getEloquentQuery(): Builder
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $query = parent::getEloquentQuery();

        if ($tenant) {
            $query->whereHas('departamento', function ($q) use ($tenant) {
                $q->where('condominio_id', $tenant->id);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Recibo')
                ->schema([
                    Forms\Components\Select::make('departamento_id')
                        ->relationship('departamento', 'numero')
                        ->label('Departamento')
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('mes')
                        ->label('Periodo')
                        ->options([
                            'Enero' => 'Enero', 'Febrero' => 'Febrero', 'Marzo' => 'Marzo', 'Abril' => 'Abril',
                            'Mayo' => 'Mayo', 'Junio' => 'Junio', 'Julio' => 'Julio', 'Agosto' => 'Agosto',
                            'Septiembre' => 'Septiembre', 'Octubre' => 'Octubre', 'Noviembre' => 'Noviembre', 'Diciembre' => 'Diciembre'
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('anio')
                        ->label('Año')
                        ->numeric()
                        ->default(date('Y'))
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('Desglose de Conceptos (S/)')
                ->description('Al ingresar los conceptos, el Total a Cobrar se calculará automáticamente.')
                ->schema([
                    Forms\Components\TextInput::make('monto_mantenimiento')
                        ->label('Cuota Mantenimiento S/')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_luz')
                        ->label('Servicios Generales (Luz) S/')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_agua')
                        ->label('Consumo Agua S/')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_mora')
                        ->label('Mora / Multas S/')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('saldo_anterior')
                        ->label('Deuda Pendiente S/')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto')
                        ->label('TOTAL A COBRAR S/')
                        ->prefix('S/')
                        ->numeric()
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('Validación y Estado del Pago')
                ->schema([
                    Forms\Components\Select::make('estado')
                        ->options([
                            'Pendiente' => '🔴 Pendiente de Pago',
                            'Pagado'    => '🟢 Pagado / Aprobado',
                        ])
                        ->default('Pendiente')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_pago')
                        ->label('Fecha de Cobro Confirmado')
                        ->default(now()),

                    Forms\Components\FileUpload::make('voucher')
                        ->label('Comprobante Adjunto (Voucher)')
                        ->image()
                        ->disk('public')
                        ->directory('vouchers'),
                ])->columns(3),
        ]);
    }

    public static function recalcularTotal(callable $set, callable $get): void
    {
        $mantenimiento = (float) ($get('monto_mantenimiento') ?? 0);
        $luz = (float) ($get('monto_luz') ?? 0);
        $agua = (float) ($get('monto_agua') ?? 0);
        $mora = (float) ($get('monto_mora') ?? 0);
        $saldo = (float) ($get('saldo_anterior') ?? 0);

        $total = $mantenimiento + $luz + $agua + $mora + $saldo;
        $set('monto', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departamento.numero')
                    ->label('Dpto')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                // 🎯 CONCEPTO DINÁMICO (SI ES NULL MUESTRA MES Y AÑO)
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Periodo / Concepto')
                    ->searchable()
                    ->state(function ($record) {
                        if (!empty($record->concepto)) {
                            return $record->concepto;
                        }
                        $mes = $record->mes ?? 'Mes Actual';
                        $anio = $record->anio ?? date('Y');
                        return 'Cuota de Mantenimiento - ' . $mes . ' ' . $anio;
                    }),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto Total')
                    ->money('PEN')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match (strtolower($state ?? '')) {
                        'pagado', 'aprobado' => 'success',
                        'pendiente'         => 'danger',
                        default             => 'gray',
                    }),

                Tables\Columns\ImageColumn::make('voucher')
                    ->label('Voucher')
                    ->disk('public')
                    ->placeholder('Efectivo / Sin foto'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('aprobarPago')
    ->label(fn (Pago $record) => !empty($record->voucher) ? 'Aprobar Pago' : 'Esperando Pago')
    ->icon(fn (Pago $record) => !empty($record->voucher) ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
    ->color(fn (Pago $record) => !empty($record->voucher) ? 'success' : 'gray')
    ->button()
    ->disabled(fn (Pago $record) => empty($record->voucher))
    ->visible(fn (Pago $record) => strtolower($record->estado ?? '') !== 'pagado')
    ->action(function (Pago $record) {
        $record->update([
            'estado' => 'Pagado',
            'fecha_pago' => now('America/Lima'),
        ]);

        Notification::make()
            ->title('Pago Aprobado')
            ->body("Se ha verificado el pago del Dpto. {$record->departamento?->numero}.")
            ->success()
            ->send();
    }),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit'   => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}