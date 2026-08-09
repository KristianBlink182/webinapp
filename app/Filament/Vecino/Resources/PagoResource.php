<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Vecino\Resources\PagoResource\Pages;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Mis Pagos y Recibos';
    protected static ?string $pluralModelLabel = 'Mis Pagos y Recibos';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('departamento_id', auth()->user()->departamento_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->label('CONCEPTO')
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
                    ->label('MONTO TOTAL')
                    ->money('PEN')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('FECHA VENCIMIENTO')
                    ->default('12 de cada mes'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('ESTADO')
                    ->badge()
                    ->color(fn (string $state = null): string => match (strtolower($state ?? '')) {
                        'pagado', 'aprobado' => 'success',
                        'en revisión', 'en revision', 'procesando' => 'warning',
                        'pendiente' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'pagado', 'aprobado' => 'Pagado',
                        'en revisión', 'en revision', 'procesando' => 'Validando Pago',
                        default => 'Pendiente',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('verPdf')
                    ->label('📄 Ver Recibo PDF')
                    ->icon('heroicon-m-document-text')
                    ->color('info')
                    ->button()
                    ->url(fn (Pago $record): string => route('pago.pdf', $record))
                    ->openUrlInNewTab(),

               Tables\Actions\EditAction::make()
    ->label(fn (Pago $record) => match (strtolower($record->estado ?? '')) {
        'pagado', 'aprobado' => '🟢 Pagado',
        'en revisión', 'en revision', 'procesando' => '🟡 Validando Pago',
        default => '💳 Pagar Recibo',
    })
    ->icon(fn (Pago $record) => match (strtolower($record->estado ?? '')) {
        'pagado', 'aprobado' => 'heroicon-m-check-badge',
        'en revisión', 'en revision', 'procesando' => 'heroicon-m-clock',
        default => 'heroicon-m-credit-card',
    })
    ->color(fn (Pago $record) => match (strtolower($record->estado ?? '')) {
        'pagado', 'aprobado' => 'success',
        'en revisión', 'en revision', 'procesando' => 'warning',
        default => 'success',
    })
    ->modalHeading('Reportar Comprobante de Pago')
    ->modalSubmitActionLabel('🚀 Enviar Pago')
    ->disabled(fn (Pago $record) => in_array(strtolower($record->estado ?? ''), ['pagado', 'aprobado', 'en revisión', 'en revision', 'procesando']))
    ->using(function (Pago $record, array $data): Pago {
        $data['estado'] = 'En Revisión';
        $record->update($data);
        return $record;
    })
    ->after(function () {
        Notification::make()
            ->title('Comprobante Enviado')
            ->body('Tu pago ha sido reportado con éxito. La administración validará el comprobante.')
            ->success()
            ->send();
    })
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
    ->formatStateUsing(function ($state, Pago $record) {
        if (!empty($state)) {
            return $state;
        }
        $mes = $record->mes ?? 'Febrero';
        $anio = $record->anio ?? date('Y');
        return "Cuota de Mantenimiento - {$mes} {$anio}";
    })
    ->disabled()
    ->dehydrated(),

                        Forms\Components\TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->prefix('S/')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2, '.', ''))
                            ->disabled()
                            ->dehydrated(),

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
            'index' => Pages\ListPagos::route('/'),
        ];
    }
}