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
                ->modalDescription('El sistema sumará y emitirá los recibos de mantenimiento, luz de áreas comunes y agua para todos los departamentos.')
                ->form([
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
                                ->required(),

                            Forms\Components\TextInput::make('anio')
                                ->label('Año')
                                ->numeric()
                                ->default(date('Y'))
                                ->required(),

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
                        ])->columns(2),

                    Forms\Components\Section::make('3. Consumo de Agua / Medidores (Opcional)')
                        ->description('Si el edificio cobra consumo de agua medido, adjunte aquí el archivo Excel/CSV.')
                        ->schema([
                            Forms\Components\Toggle::make('incluir_agua')
                                ->label('Incluir Cobro de Agua en los Recibos')
                                ->reactive(),

                            Forms\Components\TextInput::make('costo_m3')
                                ->label('Costo por m³ (S/)')
                                ->prefix('S/')
                                ->numeric()
                                ->default(5.35)
                                ->visible(fn ($get) => $get('incluir_agua')),

                            Forms\Components\FileUpload::make('archivo_agua')
                                ->label('Paso 2: Adjuntar Lecturas de Agua (.csv / .excel)')
                                ->disk('public')
                                ->directory('lecturas_agua')
                                ->visible(fn ($get) => $get('incluir_agua')),
                        ])->columns(2),
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
                                ->body("No hay gastos registrados en 'Gastos del Edificio' para {$data['mes']} {$data['anio']}. Registro los gastos o elija Monto Fijo.")
                                ->danger()
                                ->send();
                            return;
                        }
                    }

                    $lecturasAgua = [];
                    if (!empty($data['incluir_agua']) && !empty($data['archivo_agua'])) {
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
                            $lecturaActual = $lecturasAgua[$dep->numero] ?? null;
                            if ($lecturaActual !== null) {
                                $ultimoPago = Pago::where('departamento_id', $dep->id)->latest()->first();
                                $lecturaAnterior = $ultimoPago?->lectura_actual ?? 0;
                                $m3Consumidos = max(0, $lecturaActual - $lecturaAnterior);
                                $montoAgua = $m3Consumidos * (float) ($data['costo_m3'] ?? 5.35);
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