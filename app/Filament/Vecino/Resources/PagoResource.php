<?php

namespace App\Filament\Vecino\Resources;

use App\Filament\Vecino\Resources\PagoResource\Pages;
use App\Models\Pago;
use App\Models\Banco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Mis Pagos y Recibos';
    protected static ?string $modelLabel = 'Pago y Recibo';
    protected static ?string $pluralModelLabel = 'Mis Pagos y Recibos';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->where('departamento_id', $user->departamento_id ?? 1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->label('CONCEPTO')
                    ->default('Cuota de Mantenimiento')
                    ->formatStateUsing(function ($state, $record) {
                        $base = !empty($state) ? $state : ($record->concepto ?? 'Cuota de Mantenimiento');
                        $mes = $record->mes ?? 'Febrero 2026';
                        if (str_contains($base, $mes)) {
                            return $base;
                        }
                        return "{$base} - {$mes}";
                    }),

                Tables\Columns\TextColumn::make('monto')
                    ->label('MONTO TOTAL')
                    ->money('PEN')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('VENCIMIENTO')
                    ->default('12 de cada mes'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('ESTADO')
                    ->badge()
                    ->color(fn ($state): string => match (strtolower($state ?? '')) {
                        'pagado', 'al dia', 'aprobado' => 'success',
                        'en revision', 'en_revision', 'procesando', 'validando' => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => match (strtolower($state ?? '')) {
                        'pagado', 'al dia', 'aprobado' => 'Pagado',
                        'en revision', 'en_revision', 'procesando' => 'Validando Pago',
                        default => 'Pendiente',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('Ver Recibo PDF')
                    ->icon('heroicon-m-document-text')
                    ->color('primary')
                    ->button()
                    ->url(fn (Pago $record): string => url('/api/v1/vecino/pagos/' . $record->id . '/pdf'))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('reportar')
                    ->label('Pagar Recibo')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-m-credit-card')
                    ->modalHeading('Reportar Comprobante de Pago')
                    ->modalSubmitActionLabel('Enviar Pago')
                    ->visible(fn (Pago $record) => !in_array(strtolower($record->estado ?? ''), ['pagado', 'aprobado', 'en revision', 'procesando']))
                    ->action(function (array $data, Pago $record): void {
                        $voucherPath = $data['voucher'] ?? null;

                        $record->update([
                            'voucher' => $voucherPath,
                            'comprobante_pago' => $voucherPath,
                            'comprobante' => $voucherPath,
                            'metodo_pago' => 'Yape / Transferencia',
                            'estado' => 'en_revision',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Comprobante Enviado')
                            ->body('Su pago ha sido reportado con éxito. La administración validará el comprobante.')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Placeholder::make('cuentas_bancarias_info')
                            ->label('🏦 Cuentas Bancarias Oficiales del Edificio')
                            ->content(function () {
                                $condoId = auth()->user()->condominio_id ?? 1;
                                $bancos = Banco::where('condominio_id', $condoId)->get();

                                if ($bancos->isEmpty()) {
                                    return new HtmlString('<div class="p-3 bg-slate-800 text-amber-400 rounded-lg text-xs">Consulte con la administración las cuentas de depósito oficiales.</div>');
                                }

                                $html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4 p-4 bg-slate-900/90 rounded-xl border border-slate-700/60">';
                                foreach ($bancos as $banco) {
                                    $isYapePlinOnly = ($banco->numero_cuenta === 'N/A' || empty($banco->numero_cuenta) || $banco->nombre_banco === 'Yape / Plin');

                                    $html .= '<div class="p-3 bg-slate-800/80 rounded-lg border border-slate-700/40 text-xs text-slate-200 space-y-1">';

                                    if ($isYapePlinOnly) {
                                        $html .= '<div class="font-bold text-emerald-400 text-sm">📲 Yape / Plin (Billetera Digital)</div>';
                                        if (!empty($banco->yape_plin_numero)) {
                                            $html .= '<div class="text-white font-bold text-sm"><b>Número:</b> ' . e($banco->yape_plin_numero) . '</div>';
                                        }
                                        if (!empty($banco->yape_plin_titular ?? $banco->titular)) {
                                            $html .= '<div><b>Titular:</b> ' . e($banco->yape_plin_titular ?? $banco->titular) . '</div>';
                                        }
                                    } else {
                                        $html .= '<div class="font-bold text-sky-400 text-sm">🏦 ' . e($banco->nombre_banco) . ' (' . e($banco->tipo_cuenta ?? 'Corriente') . ')</div>';
                                        $html .= '<div><b>Nº Cuenta:</b> ' . e($banco->numero_cuenta) . '</div>';
                                        if (!empty($banco->cci) && $banco->cci !== 'N/A') {
                                            $html .= '<div><b>CCI:</b> ' . e($banco->cci) . '</div>';
                                        }
                                        if (!empty($banco->titular)) {
                                            $html .= '<div><b>Titular:</b> ' . e($banco->titular) . '</div>';
                                        }
                                        if ($banco->activo_yape_plin && !empty($banco->yape_plin_numero)) {
                                            $html .= '<div class="text-emerald-400 font-bold mt-1">📲 Yape / Plin: ' . e($banco->yape_plin_numero) . '</div>';
                                        }
                                    }

                                    $html .= '</div>';
                                }
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),

                        Forms\Components\TextInput::make('concepto')
                            ->label('Concepto')
                            ->disabled()
                            ->default(function (Pago $record) {
                                $base = !empty($record->concepto) ? $record->concepto : 'Cuota de Mantenimiento';
                                $mes = $record->mes ?? 'Febrero 2026';
                                if (str_contains($base, $mes)) {
                                    return $base;
                                }
                                return "{$base} - {$mes}";
                            }),

                        Forms\Components\TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->prefix('S/')
                            ->disabled()
                            ->default(fn (Pago $record) => number_format((float)($record->monto ?? 0), 2, '.', '')),

                        Forms\Components\FileUpload::make('voucher')
                            ->label('Imagen del Comprobante / Voucher')
                            ->disk('public')
                            ->directory('vouchers')
                            ->image()
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