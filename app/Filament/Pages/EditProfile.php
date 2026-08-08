<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Condominio;

class EditProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Mi Perfil y Seguridades';
    protected static ?int $navigationSort = 10;

    protected static bool $isScopedToTenant = false;

    protected static string $view = 'filament.pages.edit-profile';

    public ?array $data = [];

    public function getMaxContentWidth(): \Filament\Support\Enums\MaxWidth | string | null
    {
        return \Filament\Support\Enums\MaxWidth::Full;
    }

    public function mount(): void
    {
        $user = auth()->user();
        $tenant = \Filament\Facades\Filament::getTenant() ?? $user->departamento?->condominio;

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'telefono' => $user->telefono ?? $user->departamento?->telefono_propietario,

            // DATOS DE FACTURACIÓN PRE-REGISTRADOS (SOLO ADMINS)
            'tipo_comprobante_default' => $tenant?->tipo_comprobante_default ?? 'Boleta',
            'dni_default' => $tenant?->dni_default ?? $user->telefono,
            'nombre_default' => $tenant?->nombre_default ?? $user->name,
            'ruc_default' => $tenant?->ruc_default ?? $tenant?->ruc,
            'razon_social_default' => $tenant?->razon_social_default ?? $tenant?->nombre,
            'direccion_fiscal_default' => $tenant?->direccion_fiscal_default ?? $tenant?->direccion,
        ]);
    }

    public function form(Form $form): Form
    {
        $userRole = auth()->user()->role ?? '';
        $esAdmin = in_array($userRole, ['admin', 'administrador', 'super_admin', 'superadmin', 'master']);

        return $form
            ->schema([
                Forms\Components\Section::make('👤 Datos Personales')
                    ->description('Su nombre es administrado por la directiva. Puede actualizar su teléfono y correo.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Celular / WhatsApp de Contacto')
                            ->tel()
                            ->required()
                            ->placeholder('+51 987654321'),
                    ])->columns(3),

                // SECCIÓN DE FACTURACIÓN (VISIBLE ÚNICAMENTE PARA ADMINISTRADORES DEL EDIFICIO)
                Forms\Components\Section::make('📄 Datos Predeterminados para Facturación LIVO SaaS')
                    ->description('Configure aquí los datos con los que desea que LIVO SaaS le emita sus comprobantes de pago mensuales.')
                    ->visible($esAdmin)
                    ->schema([
                        Forms\Components\Radio::make('tipo_comprobante_default')
                            ->label('Tipo de Comprobante Preferido')
                            ->options([
                                'Boleta' => 'Boleta de Venta (Persona Natural - DNI)',
                                'Factura' => 'Factura Electrónica (Empresa Administradora / Junta - RUC)',
                            ])
                            ->live()
                            ->required(),

                        // BOLETA
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('dni_default')
                                ->label('DNI / Documento de Identidad')
                                ->required(fn ($get) => $get('tipo_comprobante_default') === 'Boleta'),

                            Forms\Components\TextInput::make('nombre_default')
                                ->label('Nombre Completo para la Boleta')
                                ->required(fn ($get) => $get('tipo_comprobante_default') === 'Boleta'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('tipo_comprobante_default') === 'Boleta'),

                        // FACTURA
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('ruc_default')
                                ->label('RUC de la Empresa / Junta (11 dígitos)')
                                ->length(11)
                                ->required(fn ($get) => $get('tipo_comprobante_default') === 'Factura'),

                            Forms\Components\TextInput::make('razon_social_default')
                                ->label('Razón Social Fiscal')
                                ->required(fn ($get) => $get('tipo_comprobante_default') === 'Factura'),

                            Forms\Components\TextInput::make('direccion_fiscal_default')
                                ->label('Dirección Fiscal de Facturación')
                                ->columnSpanFull()
                                ->required(fn ($get) => $get('tipo_comprobante_default') === 'Factura'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('tipo_comprobante_default') === 'Factura'),
                    ]),

                Forms\Components\Section::make('🔑 Cambio de Contraseña')
                    ->description('Escriba una nueva contraseña solo si desea reemplazar la actual.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->placeholder('Mínimo 6 caracteres'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->same('password')
                            ->visible(fn ($get) => !empty($get('password'))),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $user = auth()->user();
        $formData = $this->form->getState();

        $userUpdate = [
            'email' => $formData['email'],
            'telefono' => $formData['telefono'],
            'must_change_password' => false,
        ];

        if (!empty($formData['password'])) {
            $userUpdate['password'] = Hash::make($formData['password']);
        }

        $user->update($userUpdate);

        // Guardar datos de facturación en el condominio SOLO si es Administrador
        $userRole = $user->role ?? '';
        $esAdmin = in_array($userRole, ['admin', 'administrador', 'super_admin', 'superadmin', 'master']);

        if ($esAdmin) {
            $tenant = \Filament\Facades\Filament::getTenant() ?? $user->departamento?->condominio;
            if ($tenant) {
                $tenant->update([
                    'tipo_comprobante_default' => $formData['tipo_comprobante_default'] ?? 'Boleta',
                    'dni_default' => $formData['dni_default'] ?? null,
                    'nombre_default' => $formData['nombre_default'] ?? null,
                    'ruc_default' => $formData['ruc_default'] ?? null,
                    'razon_social_default' => $formData['razon_social_default'] ?? null,
                    'direccion_fiscal_default' => $formData['direccion_fiscal_default'] ?? null,
                ]);
            }
        }

        Notification::make()
            ->title('Perfil Actualizado')
            ->body('Tus datos se han guardado con éxito.')
            ->success()
            ->send();
    }
}