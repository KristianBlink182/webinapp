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
use Illuminate\Support\HtmlString;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Cobros y Recibos';
    protected static ?string $modelLabel = 'Cobro y Recibo';
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
                        ->label('Departamento')
                        ->relationship('departamento', 'numero')
                        ->searchable()
                        ->preload()
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->required(),

                    Forms\Components\Select::make('mes')
                        ->label('Periodo')
                        ->options([
                            'Enero' => 'Enero', 'Febrero' => 'Febrero', 'Marzo' => 'Marzo', 'Abril' => 'Abril',
                            'Mayo' => 'Mayo', 'Junio' => 'Junio', 'Julio' => 'Julio', 'Agosto' => 'Agosto',
                            'Septiembre' => 'Septiembre', 'Octubre' => 'Octubre', 'Noviembre' => 'Noviembre', 'Diciembre' => 'Diciembre'
                        ])
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->required(),

                    Forms\Components\TextInput::make('anio')
                        ->label('Año')
                        ->numeric()
                        ->default(date('Y'))
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('Desglose de Conceptos (S/)')
                ->description('Montos financieros del recibo.')
                ->schema([
                    Forms\Components\TextInput::make('monto_mantenimiento')
                        ->label('Cuota Mantenimiento S/')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_luz')
                        ->label('Servicios Generales (Luz) S/')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_agua')
                        ->label('Consumo Agua S/')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto_mora')
                        ->label('Mora / Multas S/')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('saldo_anterior')
                        ->label('Deuda Pendiente S/')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::recalcularTotal($set, $get)),

                    Forms\Components\TextInput::make('monto')
                        ->label('TOTAL A COBRAR S/')
                        ->prefix('S/')
                        ->numeric()
                        ->disabled(fn ($record) => strtolower($record?->estado ?? '') === 'pagado')
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('Validación y Estado del Pago')
                ->schema([
                    Forms\Components\Select::make('estado')
                        ->options([
                            'Pendiente' => '⏱ Pendiente de Pago',
                            'en_revision' => '🟡 Validando Pago',
                            'Pagado' => '🟢 Pagado / Aprobado',
                        ])
                        ->default('Pendiente')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_pago')
                        ->label('Fecha de Cobro Confirmado')
                        ->default(now()),

                    Forms\Components\Placeholder::make('voucher_preview')
                        ->label('Comprobante Adjunto (Voucher)')
                        ->content(function ($record) {
                            $filePath = $record?->voucher ?? $record?->comprobante_pago ?? $record?->comprobante;

                            if (empty($filePath)) {
                                return new HtmlString('<div class="p-3 bg-slate-800 text-slate-400 rounded-lg text-xs">Sin comprobante adjunto aún.</div>');
                            }

                            $url = asset('storage/' . ltrim($filePath, '/'));

                            return new HtmlString('
                                <div class="p-3 bg-slate-900 rounded-xl border border-slate-700/60 space-y-2 text-center">
                                    <a href="' . $url . '" target="_blank" class="inline-block overflow-hidden rounded-lg border border-slate-700 hover:opacity-90 transition">
                                        <img src="' . $url . '" alt="Voucher de Pago" class="max-h-36 object-contain mx-auto rounded-lg shadow" />
                                    </a>
                                    <div>
                                        <a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-lg transition shadow">
                                            🔍 Abrir Imagen a Pantalla Completa
                                        </a>
                                    </div>
                                </div>
                            ');
                        }),
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
                    ->label('DPTO')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                // CONCEPTO FAIL-PROOF GARANTIZADO
                Tables\Columns\TextColumn::make('concepto')
                    ->label('PERIODO / CONCEPTO')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $concepto = $record->concepto;
                        $mes = $record->mes;
                        $anio = $record->anio ?? date('Y');

                        if (!empty($concepto)) {
                            return $concepto;
                        }

                        if (!empty($mes)) {
                            return "Cuota de Mantenimiento - {$mes} {$anio}";
                        }

                        return "Cuota de Mantenimiento - Enero {$anio}";
                    }),

                Tables\Columns\TextColumn::make('monto')
                    ->label('MONTO TOTAL')
                    ->money('PEN')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('ESTADO')
                    ->badge()
                    ->color(fn (string $state = null): string => match (strtolower($state ?? '')) {
                        'pagado', 'aprobado' => 'success',
                        'en_revision', 'en revision', 'validando' => 'warning',
                        'pendiente' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state = null): string => match (strtolower($state ?? '')) {
                        'pagado', 'aprobado' => 'Pagado',
                        'en_revision', 'en revision', 'validando' => 'Validando Pago',
                        default => 'Pendiente',
                    }),

                // MINIATURA CUADRADA IMPERMEABLE A ESTILOS DE TABLA
                Tables\Columns\TextColumn::make('voucher')
                    ->label('VOUCHER')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $filePath = $record->voucher ?? $record->comprobante_pago ?? $record->comprobante;

                        if (empty($filePath)) {
                            return new HtmlString('<span class="text-xs text-slate-500">Sin foto</span>');
                        }

                        $url = asset('storage/' . ltrim($filePath, '/'));

                        return new HtmlString('
                            <a href="' . $url . '" target="_blank" style="display: inline-block !important; width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; overflow: hidden !important; border-radius: 8px !important; border: 1px solid #334155 !important; background-color: #0f172a !important;">
                                <img src="' . $url . '" style="width: 40px !important; height: 40px !important; object-fit: cover !important; display: block !important; border-radius: 8px !important;" error="this.src=\'https://via.placeholder.com/40?text=Vouch\'" />
                            </a>
                        ');
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('aprobarPago')
                    ->label(fn (Pago $record) => in_array(strtolower($record->estado ?? ''), ['en_revision', 'en revision', 'validando']) ? 'Validar Pago' : 'Aprobar Pago')
                    ->icon(fn (Pago $record) => !empty($record->voucher) || !empty($record->comprobante_pago) ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                    ->color(fn (Pago $record) => in_array(strtolower($record->estado ?? ''), ['en_revision', 'en revision', 'validando']) ? 'success' : 'gray')
                    ->button()
                    ->modalHeading(fn (Pago $record) => '🔍 Auditoría de Comprobante - Dpto. ' . ($record->departamento->numero ?? ''))
                    ->modalSubmitActionLabel('🟢 Confirmar y Aprobar Pago')
                    ->visible(fn (Pago $record) => strtolower($record->estado ?? '') !== 'pagado')
                    ->form(function (Pago $record) {
                        $filePath = $record->voucher ?? $record->comprobante_pago ?? $record->comprobante;
                        $url = !empty($filePath) ? asset('storage/' . ltrim($filePath, '/')) : null;

                        return [
                            Forms\Components\Placeholder::make('voucher_modal_preview')
                                ->label('')
                                ->content(function () use ($url, $record) {
                                    $montoFmt = number_format((float)($record->monto ?? 0), 2, '.', ',');

                                    $htmlImg = $url 
                                        ? '<a href="' . $url . '" target="_blank" class="inline-block overflow-hidden rounded-lg border border-slate-700 hover:opacity-90 transition">
                                            <img src="' . $url . '" alt="Voucher" class="max-h-36 object-contain mx-auto rounded-lg shadow-md" />
                                           </a>
                                           <div>
                                               <a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-lg transition shadow">
                                                   🔍 Ver Imagen Completa en Pestaña Nueva
                                               </a>
                                           </div>'
                                        : '<div class="p-2 bg-amber-500/20 text-amber-300 rounded-lg text-xs">⚠️ Sin archivo adjunto.</div>';

                                    return new HtmlString('
                                        <div class="p-4 bg-slate-900 rounded-xl border border-slate-700 space-y-3">
                                            <div class="grid grid-cols-2 gap-2 p-3 bg-slate-800/80 rounded-lg text-xs text-slate-200">
                                                <div><b>Departamento:</b> Dpto. ' . e($record->departamento->numero ?? 'N/E') . '</div>
                                                <div><b>Periodo:</b> ' . e($record->mes ?? '') . ' ' . e($record->anio ?? '') . '</div>
                                                <div><b>Monto Total:</b> <span class="text-emerald-400 font-bold">S/ ' . $montoFmt . '</span></div>
                                                <div><b>Método:</b> Yape / Transferencia</div>
                                            </div>
                                            <div class="text-center space-y-2">
                                                ' . $htmlImg . '
                                            </div>
                                        </div>
                                    ');
                                }),
                        ];
                    })
                    ->action(function (Pago $record) {
                        $record->update([
                            'estado' => 'Pagado',
                            'fecha_pago' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Pago Aprobado')
                            ->body("Se ha verificado y aprobado el pago del Dpto. {$record->departamento->numero}.")
                            ->success()
                            ->send();
                    })
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('rechazarPago')
                            ->label('🔴 Rechazar Voucher Invalido')
                            ->color('danger')
                            ->button()
                            ->action(function (Pago $record) {
                                $record->update([
                                    'estado' => 'Pendiente',
                                    'voucher' => null,
                                    'comprobante_pago' => null,
                                    'comprobante' => null,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Comprobante Rechazado')
                                    ->body("Se ha rechazado el voucher del Dpto. {$record->departamento->numero}. El recibo volvió a estado Pendiente.")
                                    ->warning()
                                    ->send();
                            }),
                    ]),

                Tables\Actions\EditAction::make()
                    ->visible(fn (Pago $record) => strtolower($record->estado ?? '') !== 'pagado'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}