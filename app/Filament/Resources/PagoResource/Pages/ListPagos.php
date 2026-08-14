<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use App\Models\Pago;
use App\Models\Gasto;
use App\Models\Departamento;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;

class ListPagos extends ListRecords
{
    protected static string $resource = PagoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON LOS BOTONES INTEGRADOS
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '💳',
            'badge'       => 'Finanzas del Edificio',
            'title'       => 'Gestión de Cobros & Recibos de Mantenimiento',
            'description' => 'Emisión masiva de cuotas del mes, cobranza de luz/agua por medidor, verificación de vouchers y aprobación de pagos.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generarCuotas')
                ->label('⚡ Generar Cuotas del Mes')
                ->icon('heroicon-m-calculator')
                ->color('success')
                ->modalHeading('⚡ Generación Masiva de Recibos Mensuales')
                ->modalSubmitActionLabel('🚀 Confirmar y Emitir Cuotas')
                ->steps([
                    Forms\Components\Wizard\Step::make('Parámetros de Cobro')
                        ->description('Defina el periodo, mantenimientos, luz y agua del mes')
                        ->afterValidation(function ($get) {
                            $mes = $get('mes');
                            $anio = $get('anio');
                            $tenant = \Filament\Facades\Filament::getTenant();
                            $condoId = $tenant?->id ?? auth()->user()->condominio_id;

                            // 🚨 BLOQUEO STRICTO 1: VALIDAR SI EL MES YA FUE EMITIDO
                            $yaExiste = Pago::whereHas('departamento', function ($q) use ($condoId) {
                                    if ($condoId) {
                                        $q->where('condominio_id', $condoId);
                                    }
                                })
                                ->where('mes', $mes)
                                ->where('anio', $anio)
                                ->exists();

                            if ($yaExiste) {
                                Notification::make()
                                    ->title('Emisión Bloqueada')
                                    ->body("Ya existen recibos emitidos para {$mes} {$anio}. No se puede volver a generar este mes.")
                                    ->danger()
                                    ->persistent()
                                    ->send();

                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'mes' => "Ya existen recibos emitidos para {$mes} {$anio} en este edificio. Elija un mes pendiente.",
                                ]);
                            }

                            // 🚨 BLOQUEO STRICTO 2: VALIDAR PRORRATEO SIN GASTOS REGISTRADOS
                            if ($get('tipo_cobro') === 'prorrateo') {
                                $totalGastos = Gasto::where('condominio_id', $condoId)
                                    ->where('mes', $mes)
                                    ->where('anio', $anio)
                                    ->sum('monto') ?? 0;

                                if ($totalGastos <= 0) {
                                    Notification::make()
                                        ->title('Sin Gastos Registrados')
                                        ->body("No hay gastos registrados en 'Gastos del Edificio' para {$mes} {$anio}. Registre los gastos primero o elija Monto Fijo.")
                                        ->danger()
                                        ->persistent()
                                        ->send();

                                    throw \Illuminate\Validation\ValidationException::withMessages([
                                        'tipo_cobro' => "No hay gastos registrados para {$mes} {$anio}. Registre los gastos en 'Gastos del Edificio' o cambie a Monto Fijo.",
                                    ]);
                                }
                            }
                        })
                        ->schema([
                            Forms\Components\Section::make('1. Periodo y Mantenimiento Base')
                                ->schema([
                                    Forms\Components\Select::make('mes')
                                        ->label('Mes del Cobro')
                                        ->options([
                                            'Enero' => 'Enero', 'Febrero' => 'Febrero', 'Marzo' => 'Marzo', 'Abril' => 'Abril',
                                            'Mayo' => 'Mayo', 'Junio' => 'Junio', 'Julio' => 'Julio', 'Agosto' => 'Agosto',
                                            'Septiembre' => 'Septiembre', 'Octubre' => 'Octubre', 'Noviembre' => 'Noviembre', 'Diciembre' => 'Diciembre'
                                        ])
                                        ->default('Enero')
                                        ->reactive()
                                        ->required(),

                                    Forms\Components\TextInput::make('anio')
                                        ->label('Año')
                                        ->numeric()
                                        ->default(date('Y'))
                                        ->reactive()
                                        ->required(),

                                    Forms\Components\Placeholder::make('alerta_mes_ya_emitido')
                                        ->label('')
                                        ->columnSpanFull()
                                        ->visible(function ($get) {
                                            $mes = $get('mes');
                                            $anio = $get('anio');
                                            if (empty($mes) || empty($anio)) return false;

                                            $tenant = \Filament\Facades\Filament::getTenant();
                                            $condoId = $tenant?->id ?? auth()->user()->condominio_id;

                                            return Pago::whereHas('departamento', function ($q) use ($condoId) {
                                                if ($condoId) {
                                                    $q->where('condominio_id', $condoId);
                                                }
                                            })
                                            ->where('mes', $mes)
                                            ->where('anio', $anio)
                                            ->exists();
                                        })
                                        ->content(function ($get) {
                                            $mes = $get('mes');
                                            $anio = $get('anio');
                                            return new \Illuminate\Support\HtmlString("
                                                <div class='p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 text-red-800 dark:text-red-300 text-xs font-semibold flex items-center gap-2'>
                                                    🚨 <span><strong>Atención:</strong> Ya existen recibos emitidos para <strong>{$mes} {$anio}</strong>. Elija un mes pendiente para continuar.</span>
                                                </div>
                                            ");
                                        }),

                                    Forms\Components\Radio::make('tipo_cobro')
                                        ->label('Modalidad de Mantenimiento')
                                        ->options([
                                            'fijo'      => '💵 Monto Fijo (Cuota igual para todos)',
                                            'prorrateo' => '📊 Prorrateo por % (Suma de Gastos del Mes x % de cada dpto)',
                                        ])
                                        ->default('fijo')
                                        ->reactive()
                                        ->required(),

                                    Forms\Components\TextInput::make('monto_fijo')
                                        ->label('Monto Fijo de Mantenimiento (S/)')
                                        ->prefix('S/')
                                        ->numeric()
                                        ->visible(fn ($get) => $get('tipo_cobro') === 'fijo'),

                                    Forms\Components\Placeholder::make('info_prorrateo')
                                        ->label('Aviso de Prorrateo')
                                        ->content('El sistema sumará automáticamente todos los comprobantes registrados en "Gastos del Edificio" de este mes y aplicará el % de participación de cada departamento.')
                                        ->visible(fn ($get) => $get('tipo_cobro') === 'prorrateo'),

                                    Forms\Components\Placeholder::make('alerta_sin_gastos')
                                        ->label('')
                                        ->columnSpanFull()
                                        ->visible(function ($get) {
                                            if ($get('tipo_cobro') !== 'prorrateo') return false;
                                            $mes = $get('mes');
                                            $anio = $get('anio');
                                            if (empty($mes) || empty($anio)) return false;

                                            $tenant = \Filament\Facades\Filament::getTenant();
                                            $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

                                            $totalGastos = Gasto::where('condominio_id', $condoId)
                                                ->where('mes', $mes)
                                                ->where('anio', $anio)
                                                ->sum('monto') ?? 0;

                                            return $totalGastos <= 0;
                                        })
                                        ->content(function ($get) {
                                            $mes = $get('mes');
                                            $anio = $get('anio');
                                            return new \Illuminate\Support\HtmlString("
                                                <div class='p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 text-amber-800 dark:text-amber-300 text-xs font-semibold flex items-center gap-2'>
                                                    ⚠️ <span><strong>Sin Gastos Registrados:</strong> No existen gastos registrados en 'Gastos del Edificio' para <strong>{$mes} {$anio}</strong>. Debe registrarlos primero o cambiar a Monto Fijo.</span>
                                                </div>
                                            ");
                                        }),
                                ])->columns(2),

                            Forms\Components\Section::make('2. Luz de Servicios Generales (Pasillos, Ascensores, Bombas)')
                                ->description('Ingrese la factura de luz común del edificio para dividirla entre los departamentos.')
                                ->schema([
                                    Forms\Components\Toggle::make('incluir_luz')
                                        ->label('Incluir Cobro de Luz de Áreas Comunes')
                                        ->reactive(),

                                    Forms\Components\TextInput::make('total_recibo_luz')
                                        ->label('Monto Total de Luz del Edificio (S/)')
                                        ->prefix('S/')
                                        ->numeric()
                                        ->placeholder('Ej: 1200.00')
                                        ->visible(fn ($get) => $get('incluir_luz')),

                                    Forms\Components\Radio::make('forma_division_luz')
                                        ->label('Distribución del Cobro de Luz')
                                        ->options([
                                            'igual'      => 'Dividir por partes iguales entre todos los dptos',
                                            'porcentaje' => 'Dividir según el % de participación de cada dpto',
                                        ])
                                        ->default('igual')
                                        ->visible(fn ($get) => $get('incluir_luz')),

                                    Forms\Components\FileUpload::make('archivo_luz')
                                        ->label('Adjuntar Factura/Recibo de Luz (Opcional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('recibos_luz')
                                        ->visible(fn ($get) => $get('incluir_luz')),
                                ])->columns(2),

                            Forms\Components\Section::make('3. Consumo de Agua del Edificio')
                                ->description('Seleccione si el agua es un recibo común del edificio o por medidores individuales.')
                                ->schema([
                                    Forms\Components\Toggle::make('incluir_agua')
                                        ->label('Incluir Cobro de Agua en los Recibos')
                                        ->reactive(),

                                    Forms\Components\Radio::make('modalidad_agua')
                                        ->label('Tipo de Cobro de Agua')
                                        ->options([
                                            'global' => '🏢 Recibo Global del Edificio (Factura Común)',
                                            'medidores' => '💧 Consumo Medido por m³ (Lecturas en Excel)',
                                        ])
                                        ->default('global')
                                        ->visible(fn ($get) => $get('incluir_agua'))
                                        ->reactive(),

                                    // CASO A: AGUA GLOBAL DEL EDIFICIO
                                    Forms\Components\TextInput::make('total_recibo_agua')
                                        ->label('Monto Total Recibo de Agua del Edificio (S/)')
                                        ->prefix('S/')
                                        ->numeric()
                                        ->placeholder('Ej: 1500.00')
                                        ->visible(fn ($get) => $get('incluir_agua') && $get('modalidad_agua') === 'global'),

                                    Forms\Components\Radio::make('forma_division_agua')
                                        ->label('Distribución del Cobro de Agua')
                                        ->options([
                                            'igual'      => 'Dividir por partes iguales entre todos los dptos',
                                            'porcentaje' => 'Dividir según el % de participación de cada dpto',
                                        ])
                                        ->default('igual')
                                        ->visible(fn ($get) => $get('incluir_agua') && $get('modalidad_agua') === 'global'),

                                    Forms\Components\FileUpload::make('archivo_recibo_agua')
                                        ->label('Adjuntar Factura/Recibo de Agua (Opcional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('recibos_agua')
                                        ->visible(fn ($get) => $get('incluir_agua') && $get('modalidad_agua') === 'global'),

                                    // CASO B: CONSUMO MEDIDO POR M3 (EXCEL)
                                    Forms\Components\TextInput::make('costo_m3')
                                        ->label('Costo por m³ (S/)')
                                        ->prefix('S/')
                                        ->numeric()
                                        ->default(5.35)
                                        ->visible(fn ($get) => $get('incluir_agua') && $get('modalidad_agua') === 'medidores'),

                                    Forms\Components\FileUpload::make('archivo_agua')
                                        ->label('Adjuntar Lecturas de Agua (.csv / .excel)')
                                        ->disk('public')
                                        ->directory('lecturas_agua')
                                        ->visible(fn ($get) => $get('incluir_agua') && $get('modalidad_agua') === 'medidores'),
                                ])->columns(2),
                        ]),

                    Forms\Components\Wizard\Step::make('Resumen y Confirmación')
                        ->description('Verifique la información y el % de participación de cada departamento')
                        ->schema([
                            Forms\Components\Placeholder::make('resumen_cobro')
                                ->label('')
                                ->content(function ($get) {
                                    $tenant = \Filament\Facades\Filament::getTenant();
                                    $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;
                                    $departamentos = Departamento::where('condominio_id', $condoId)->orderBy('numero', 'asc')->get();
                                    $totalDepas = $departamentos->count();

                                    $mes = $get('mes') ?? 'Enero';
                                    $anio = $get('anio') ?? date('Y');
                                    $tipoCobroVal = $get('tipo_cobro') ?? 'fijo';
                                    
                                    $totalGastos = 0;
                                    if ($tipoCobroVal === 'prorrateo') {
                                        $totalGastos = Gasto::where('condominio_id', $condoId)
                                            ->where('mes', $mes)
                                            ->where('anio', $anio)
                                            ->sum('monto') ?? 0;
                                    }

                                    $tipoCobroText = $tipoCobroVal === 'prorrateo' 
                                        ? ('📊 Prorrateo Gastos (S/ ' . number_format($totalGastos, 2) . ')') 
                                        : ('💵 Monto Fijo de S/ ' . number_format((float)($get('monto_fijo') ?? 0), 2));

                                    $montoLuzText = $get('incluir_luz') 
                                        ? ('💡 S/ ' . number_format((float)($get('total_recibo_luz') ?? 0), 2) . ' (' . ($get('forma_division_luz') === 'porcentaje' ? 'Por % dpto' : 'Partes iguales') . ')')
                                        : '❌ No incluido';

                                    $montoAguaText = '❌ No incluido';
                                    if (!empty($get('incluir_agua'))) {
                                        if (($get('modalidad_agua') ?? 'global') === 'global') {
                                            $montoAguaText = '🏢 Global S/ ' . number_format((float)($get('total_recibo_agua') ?? 0), 2) . ' (' . ($get('forma_division_agua') === 'porcentaje' ? 'Por % dpto' : 'Partes iguales') . ')';
                                        } else {
                                            $montoAguaText = '💧 Medido S/ ' . number_format((float)($get('costo_m3') ?? 5.35), 2) . ' por m³';
                                        }
                                    }

                                    // Generar Filas Dinámicas de la Tabla por Dpto
                                    $filasTablaDptos = '';
                                    foreach ($departamentos as $dep) {
                                        $pct = number_format((float)($dep->porcentaje_participacion ?? 0), 2);
                                        
                                        $mantenimientoDpto = 0;
                                        if ($tipoCobroVal === 'prorrateo') {
                                            $mantenimientoDpto = $totalGastos * (($dep->porcentaje_participacion ?? 0) / 100);
                                        } else {
                                            $mantenimientoDpto = (float)($get('monto_fijo') ?? 0);
                                        }

                                        $luzDpto = 0;
                                        if (!empty($get('incluir_luz')) && !empty($get('total_recibo_luz'))) {
                                            $totalLuzVal = (float)$get('total_recibo_luz');
                                            if (($get('forma_division_luz') ?? 'igual') === 'igual') {
                                                $luzDpto = $totalDepas > 0 ? ($totalLuzVal / $totalDepas) : 0;
                                            } else {
                                                $luzDpto = $totalLuzVal * (($dep->porcentaje_participacion ?? 0) / 100);
                                            }
                                        }

                                        $aguaDpto = 0;
                                        if (!empty($get('incluir_agua')) && ($get('modalidad_agua') ?? 'global') === 'global' && !empty($get('total_recibo_agua'))) {
                                            $totalAguaVal = (float)$get('total_recibo_agua');
                                            if (($get('forma_division_agua') ?? 'igual') === 'igual') {
                                                $aguaDpto = $totalDepas > 0 ? ($totalAguaVal / $totalDepas) : 0;
                                            } else {
                                                $aguaDpto = $totalAguaVal * (($dep->porcentaje_participacion ?? 0) / 100);
                                            }
                                        }

                                        $totalDpto = $mantenimientoDpto + $luzDpto + $aguaDpto;

                                        $filasTablaDptos .= "
                                            <tr class='hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-200/60 dark:border-slate-800'>
                                                <td class='px-3 py-2 font-bold text-slate-900 dark:text-white'>Dpto. {$dep->numero}</td>
                                                <td class='px-3 py-2 text-center font-semibold text-slate-600 dark:text-slate-400'>{$pct}%</td>
                                                <td class='px-3 py-2 text-right text-slate-700 dark:text-slate-300'>S/ " . number_format($mantenimientoDpto, 2) . "</td>
                                                <td class='px-3 py-2 text-right text-slate-700 dark:text-slate-300'>S/ " . number_format($luzDpto, 2) . "</td>
                                                <td class='px-3 py-2 text-right text-slate-700 dark:text-slate-300'>S/ " . number_format($aguaDpto, 2) . "</td>
                                                <td class='px-3 py-2 text-right font-bold text-sky-600 dark:text-sky-400'>S/ " . number_format($totalDpto, 2) . "</td>
                                            </tr>
                                        ";
                                    }

                                    return new \Illuminate\Support\HtmlString("
                                        <div class='rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 text-slate-800 dark:text-slate-100 shadow-sm'>
                                            <div class='flex items-center justify-between pb-3 mb-4 border-b border-slate-200 dark:border-slate-800'>
                                                <div>
                                                    <h3 class='text-base font-bold text-slate-900 dark:text-white flex items-center gap-2'>
                                                        🏢 Edificio: <span class='text-sky-600 dark:text-sky-400'>{$tenant?->nombre}</span>
                                                    </h3>
                                                    <p class='text-xs text-slate-500 dark:text-slate-400 mt-0.5'>Total de <strong>{$totalDepas}</strong> departamentos registrados</p>
                                                </div>
                                                <div class='text-right'>
                                                    <span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300'>
                                                        Periodo: {$mes} {$anio}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class='grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 text-xs'>
                                                <div class='p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/50'>
                                                    <span class='text-slate-500 dark:text-slate-400 block font-medium'>💵 Mantenimiento</span>
                                                    <strong class='text-slate-800 dark:text-slate-200 text-xs mt-0.5 block'>{$tipoCobroText}</strong>
                                                </div>
                                                <div class='p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/50'>
                                                    <span class='text-slate-500 dark:text-slate-400 block font-medium'>💡 Luz Común</span>
                                                    <strong class='text-slate-800 dark:text-slate-200 text-xs mt-0.5 block'>{$montoLuzText}</strong>
                                                </div>
                                                <div class='p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/50'>
                                                    <span class='text-slate-500 dark:text-slate-400 block font-medium'>💧 Agua Edificio</span>
                                                    <strong class='text-slate-800 dark:text-slate-200 text-xs mt-0.5 block'>{$montoAguaText}</strong>
                                                </div>
                                            </div>

                                            <h4 class='text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2'>
                                                📊 Desglose Estimado por Departamento (% Participación)
                                            </h4>
                                            <div class='max-h-52 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700/80'>
                                                <table class='w-full text-xs text-left'>
                                                    <thead class='bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold sticky top-0'>
                                                        <tr>
                                                            <th class='px-3 py-2'>Dpto</th>
                                                            <th class='px-3 py-2 text-center'>% Part.</th>
                                                            <th class='px-3 py-2 text-right'>Mantenimiento</th>
                                                            <th class='px-3 py-2 text-right'>Luz</th>
                                                            <th class='px-3 py-2 text-right'>Agua</th>
                                                            <th class='px-3 py-2 text-right font-bold'>Total Base</th>
                                                        </tr>
                                                    </thead >
<tbody class='bg-white dark:bg-slate-900'>
" . $filasTablaDptos . "
</tbody>
</table>
                                            </div>

                                            <div class='mt-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-300 text-xs font-medium'>
                                                ⚠️ Al hacer clic en <strong>'Confirmar y Emitir Cuotas'</strong>, se generarán e instalarán automáticamente las cuotas individuales para los <strong>{$totalDepas} departamentos</strong>.
                                            </div>
                                        </div>
                                    ");
                                }),
                        ]),
                ])
                ->action(function (array $data) {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

                    $totalGastos = 0;
                    if ($data['tipo_cobro'] === 'prorrateo') {
                        $totalGastos = Gasto::where('condominio_id', $condoId)
                            ->where('mes', $data['mes'])
                            ->where('anio', $data['anio'])
                            ->sum('monto') ?? 0;

                        if ($totalGastos <= 0) {
                            Notification::make()
                                ->title('Sin Gastos Registrados')
                                ->body("No hay gastos registrados en 'Gastos del Edificio' para {$data['mes']} {$data['anio']}. Registre los gastos o elija Monto Fijo.")
                                ->danger()
                                ->send();
                            return;
                        }
                    }

                    $lecturasAgua = [];
                    if (!empty($data['incluir_agua']) && ($data['modalidad_agua'] ?? '') === 'medidores' && !empty($data['archivo_agua'])) {
                        $filePath = storage_path('app/public/' . $data['archivo_agua']);
                        if (file_exists($filePath) && ($handle = fopen($filePath, 'r')) !== false) {
                            $firstLine = fgets($handle);
                            $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
                            rewind($handle);

                            $rowNum = 0;
                            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                                $rowNum++;
                                if ($rowNum === 1 || empty($row[0])) continue;
                                $num = trim($row[0]);
                                $lectura = (float) ($row[1] ?? 0);
                                $lecturasAgua[$num] = $lectura;
                            }
                            fclose($handle);
                        }
                    }

                    $departamentos = Departamento::where('condominio_id', $condoId)->get();
                    $totalDepas = $departamentos->count();
                    $generados = 0;

                    foreach ($departamentos as $dep) {
                        $montoMantenimiento = 0;
                        if ($data['tipo_cobro'] === 'prorrateo') {
                            $montoMantenimiento = $totalGastos * (($dep->porcentaje_participacion ?? 0) / 100);
                        } else {
                            $montoMantenimiento = (float) ($data['monto_fijo'] ?? 0);
                        }

                        $montoLuz = 0;
                        if (!empty($data['incluir_luz']) && !empty($data['total_recibo_luz'])) {
                            $totalLuz = (float) $data['total_recibo_luz'];
                            if ($data['forma_division_luz'] ?? 'igual' === 'igual') {
                                $montoLuz = $totalDepas > 0 ? ($totalLuz / $totalDepas) : 0;
                            } else {
                                $montoLuz = $totalLuz * (($dep->porcentaje_participacion ?? 0) / 100);
                            }
                        }

                        $montoAgua = 0;
                        $lecturaActual = null;
                        if (!empty($data['incluir_agua'])) {
                            if (($data['modalidad_agua'] ?? 'global') === 'global' && !empty($data['total_recibo_agua'])) {
                                $totalAgua = (float) $data['total_recibo_agua'];
                                if ($data['forma_division_agua'] ?? 'igual' === 'igual') {
                                    $montoAgua = $totalDepas > 0 ? ($totalAgua / $totalDepas) : 0;
                                } else {
                                    $montoAgua = $totalAgua * (($dep->porcentaje_participacion ?? 0) / 100);
                                }
                            } elseif (($data['modalidad_agua'] ?? '') === 'medidores') {
                                $lecturaActual = $lecturasAgua[$dep->numero] ?? null;
                                if ($lecturaActual !== null) {
                                    $ultimoPago = Pago::where('departamento_id', $dep->id)->latest()->first();
                                    $lecturaAnterior = $ultimoPago?->lectura_actual ?? 0;
                                    $m3Consumidos = max(0, $lecturaActual - $lecturaAnterior);
                                    $montoAgua = $m3Consumidos * (float) ($data['costo_m3'] ?? 5.35);
                                }
                            }
                        }

                        $montoTotal = $montoMantenimiento + $montoLuz + $montoAgua;

                        Pago::updateOrCreate(
                            [
                                'departamento_id' => $dep->id,
                                'mes'             => $data['mes'],
                                'anio'            => $data['anio'],
                            ],
                            [
                                'condominio_id'       => $condoId,
                                'concepto'            => 'Cuota de Mantenimiento - ' . $data['mes'] . ' ' . $data['anio'],
                                'monto_mantenimiento' => $montoMantenimiento,
                                'monto_luz'           => $montoLuz,
                                'monto_agua'          => $montoAgua,
                                'lectura_actual'      => $lecturaActual,
                                'monto'               => $montoTotal,
                                'estado'              => 'Pendiente',
                            ]
                        );
                        $generados++;
                    }

                    Notification::make()
                        ->title('¡Cuotas Generadas con Éxito!')
                        ->body("Se han emitido {$generados} recibos completísimos para {$data['mes']} {$data['anio']}.")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Nuevo Pago Manual')
                ->createAnother(false),
        ];
    }
}