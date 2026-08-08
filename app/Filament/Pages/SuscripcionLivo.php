<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Livewire\WithPagination;

class SuscripcionLivo extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Suscripción LIVO';
    protected static ?string $title = 'Membresía & Pago del Sistema LIVO';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.suscripcion-livo';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        $this->form->fill([
            'tipo_comprobante' => $tenant?->tipo_comprobante_default ?? 'Boleta',
            'dni' => $tenant?->dni_default ?? auth()->user()->telefono,
            'nombre' => $tenant?->nombre_default ?? auth()->user()->name,
            'ruc' => $tenant?->ruc_default ?? $tenant?->ruc,
            'razon_social' => $tenant?->razon_social_default ?? $tenant?->nombre,
            'direccion_fiscal' => $tenant?->direccion_fiscal_default ?? $tenant?->direccion,
            'cambiar_datos' => false,
        ]);
    }

    public function form(Form $form): Form
    {
        $tenant = Filament::getTenant();
        $precioBase = $tenant?->precio_mensual_saas ?? 100;

        return $form
            ->schema([
                Forms\Components\Section::make('📄 Datos de Facturación y Emisión de Comprobante')
                    ->description('Seleccione el tipo de comprobante que requiere. Si elige Factura, se calculará automáticamente el +18% de IGV.')
                    ->schema([
                        Forms\Components\Radio::make('tipo_comprobante')
                            ->label('Tipo de Comprobante Requerido')
                            ->options([
                                'Boleta' => 'Boleta de Venta (Persona Natural - S/ ' . number_format($precioBase, 2) . ')',
                                'Factura' => 'Factura Electrónica (RUC + 18% IGV = S/ ' . number_format($precioBase * 1.18, 2) . ')',
                            ])
                            ->live()
                            ->required(),

                        Forms\Components\Toggle::make('cambiar_datos')
                            ->label('Deseo usar datos de facturación diferentes únicamente para este mes')
                            ->live(),

                        // CAMPOS FACTURA
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('ruc')
                                ->label('RUC de la Empresa / Junta')
                                ->length(11)
                                ->required(fn ($get) => $get('tipo_comprobante') === 'Factura'),

                            Forms\Components\TextInput::make('razon_social')
                                ->label('Razón Social Fiscal')
                                ->required(fn ($get) => $get('tipo_comprobante') === 'Factura'),

                            Forms\Components\TextInput::make('direccion_fiscal')
                                ->label('Dirección Fiscal')
                                ->columnSpanFull()
                                ->required(fn ($get) => $get('tipo_comprobante') === 'Factura'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('tipo_comprobante') === 'Factura' && $get('cambiar_datos')),

                        // CAMPOS BOLETA
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('dni')
                                ->label('DNI / Documento Identidad')
                                ->required(fn ($get) => $get('tipo_comprobante') === 'Boleta'),

                            Forms\Components\TextInput::make('nombre')
                                ->label('Nombre Completo')
                                ->required(fn ($get) => $get('tipo_comprobante') === 'Boleta'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('tipo_comprobante') === 'Boleta' && $get('cambiar_datos')),
                    ]),

                Forms\Components\Section::make('Subir Comprobante de Pago Mensual')
                    ->description('Adjunta la foto o captura de tu Yape, Plin o transferencia bancaria a LIVO.')
                    ->schema([
                        Forms\Components\FileUpload::make('voucher_saas')
                            ->label('Adjuntar Voucher de Pago (SaaS)')
                            ->disk('public')
                            ->directory('vouchers_saas')
                            ->visibility('public')
                            ->previewable(false)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'application/pdf'])
                            ->maxSize(10240)
                            ->required(),
                    ])
            ])
            ->statePath('data');
    }

    public function registrarPago(): void
    {
        $data = $this->form->getState();
        $tenant = Filament::getTenant();

        if ($tenant) {
            $montoBase = $tenant->precio_mensual_saas ?? 100;
            $esFactura = ($data['tipo_comprobante'] ?? 'Boleta') === 'Factura';
            $montoIgv = $esFactura ? ($montoBase * 0.18) : 0;
            $montoTotal = $montoBase + $montoIgv;

            $tenant->update([
                'voucher_saas' => $data['voucher_saas'],
                'estado_pago_saas' => 'Pago por Verificar',
            ]);

            // Guardar datos predeterminados en el condominio si no fueron cambiados
            if (empty($data['cambiar_datos'])) {
                $tenant->update([
                    'tipo_comprobante_default' => $data['tipo_comprobante'],
                    'dni_default' => $data['dni'] ?? null,
                    'nombre_default' => $data['nombre'] ?? null,
                    'ruc_default' => $data['ruc'] ?? null,
                    'razon_social_default' => $data['razon_social'] ?? null,
                    'direccion_fiscal_default' => $data['direccion_fiscal'] ?? null,
                ]);
            }

            // Guardar registro congelado en el historial de Pagos SaaS
            if (\Illuminate\Support\Facades\Schema::hasTable('pago_saas')) {
                \App\Models\PagoSaaS::create([
                    'condominio_id' => $tenant->id,
                    'plan' => $tenant->plan_saas ?? 'Básico',
                    'monto' => $montoTotal,
                    'monto_base' => $montoBase,
                    'monto_igv' => $montoIgv,
                    'monto_total' => $montoTotal,
                    'tipo_comprobante' => $data['tipo_comprobante'],
                    'dni' => $data['dni'] ?? $tenant->dni_default,
                    'nombre' => $data['nombre'] ?? $tenant->nombre_default,
                    'ruc' => $data['ruc'] ?? $tenant->ruc_default,
                    'razon_social' => $data['razon_social'] ?? $tenant->razon_social_default,
                    'direccion_fiscal' => $data['direccion_fiscal'] ?? $tenant->direccion_fiscal_default,
                    'voucher' => $data['voucher_saas'],
                    'estado' => 'Pago por Verificar',
                ]);
            }

            // Limpiar el campo del voucher
            $this->form->fill([
                'tipo_comprobante' => $data['tipo_comprobante'],
                'voucher_saas' => null,
                'cambiar_datos' => false,
            ]);

            Notification::make()
                ->title('Comprobante Registrado')
                ->body('Tu voucher por S/ ' . number_format($montoTotal, 2) . ' fue enviado con éxito. El equipo Master emitirá tu ' . $data['tipo_comprobante'] . '.')
                ->success()
                ->send();
        }
    }

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();

        $historialPagos = [];
        if ($tenant && \Illuminate\Support\Facades\Schema::hasTable('pago_saas')) {
            $historialPagos = \App\Models\PagoSaaS::where('condominio_id', $tenant->id)
                ->latest()
                ->paginate(5);
        }

        return [
            'historialPagos' => $historialPagos,
        ];
    }
}