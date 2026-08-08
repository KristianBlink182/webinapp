<?php

namespace App\Filament\Vecino\Resources;

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
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Mis Pagos y Recibos';
    protected static ?string $pluralModelLabel = 'Mis Pagos y Recibos';
    protected static ?int $navigationSort = 1;

    // 🔴 INSIGNIA NEÓN EN EL MENÚ LATERAL SI TIENE RECIBOS PENDIENTES DE PAGO
    public static function getNavigationBadge(): ?string
    {
        $depaId = auth()->user()->departamento_id;
        if (!$depaId) return null;

        $pendientes = Pago::where('departamento_id', $depaId)
            ->whereIn('estado', ['Pendiente', 'pendiente', 'Pendiente de Pago'])
            ->count();

        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('departamento_id', auth()->user()->departamento_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              // 🎯 CONCEPTO GARANTIZADO (MES Y AÑO SI VIENE EN BLANCO)
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->weight('bold')
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
                    ->weight('black')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Fecha Vencimiento')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-clock')
                    ->placeholder('12 de cada mes'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match (strtolower($state ?? '')) {
                        'pendiente', 'pendiente de pago' => 'danger',
                        'en revisión', 'procesando', 'en revision' => 'warning',
                        'aprobado', 'pagado' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // BOTÓN 1: VER RECIBO DETALLADO EN PDF
                Tables\Actions\Action::make('verPDF')
                    ->label('Ver Recibo PDF')
                    ->icon('heroicon-m-document-text')
                    ->color('info')
                    ->button()
                    ->url(fn (Pago $record): string => route('pago.pdf', $record))
                    ->openUrlInNewTab(),

                // BOTÓN 2: SUBIR VOUCHER DE PAGO
                Tables\Actions\EditAction::make()
                    ->label('Subir Voucher')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('success')
                    ->button(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Reportar Comprobante de Pago')
                    ->description('Adjunta la foto o captura de tu Yape, Plin o transferencia bancaria.')
                    ->schema([
                        Forms\Components\TextInput::make('concepto')
                            ->label('Concepto')
                            ->disabled(),

                        Forms\Components\TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->prefix('S/')
                            ->disabled(),

                        Forms\Components\FileUpload::make('voucher')
                            ->label('Imagen del Comprobante / Voucher')
                            ->image()
                            ->disk('public')
                            ->directory('vouchers')
                            ->required(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PagoResource\Pages\ListPagos::route('/'),
        ];
    }
}