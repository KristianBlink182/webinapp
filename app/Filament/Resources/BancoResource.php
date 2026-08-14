<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BancoResource\Pages;
use App\Models\Banco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;
use Filament\Facades\Filament;

class BancoResource extends Resource
{
    protected static ?string $model = Banco::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Cuentas Bancarias';
    protected static ?string $modelLabel = 'Cuenta Bancaria';
    protected static ?string $pluralModelLabel = 'Cuentas Bancarias';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool 
    { 
        return true; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('condominio_id')
                    ->default(function () {
                        return Filament::getTenant()?->id ?? auth()->user()->condominio_id ?? 1;
                    }),

                Section::make('Información de la Cuenta Bancaria (Opcional si es solo Yape/Plin)')
                    ->description('Registre la cuenta bancaria del edificio. Si solo va a configurar Yape/Plin o Tarjeta, puede dejar estos campos vacíos.')
                    ->schema([
                        Select::make('nombre_banco')
                            ->label('Banco / Entidad Financiera')
                            ->options([
                                'BCP' => 'BCP (Banco de Crédito del Perú)',
                                'BBVA' => 'BBVA Perú',
                                'Interbank' => 'Interbank',
                                'Scotiabank' => 'Scotiabank Perú',
                                'Yape / Plin' => '📲 Yape / Plin (Exclusivo)',
                                'BanBif' => 'BanBif (Banco Interamericano de Finanzas)',
                                'Banco Pichincha' => 'Banco Pichincha',
                                'Banco GNB' => 'Banco GNB Perú',
                                'Banco Falabella' => 'Banco Falabella',
                                'Banco Ripley' => 'Banco Ripley',
                                'Banco Comercio' => 'Banco Comercio',
                                'Mibanco' => 'Mibanco',
                                'Caja Arequipa' => 'Caja Arequipa',
                                'Caja Huancayo' => 'Caja Huancayo',
                                'Caja Piura' => 'Caja Piura',
                                'Caja Cusco' => 'Caja Cusco',
                                'Otro' => 'Otro Banco / Financiera',
                            ])
                            ->searchable()
                            ->default('BCP'),

                        TextInput::make('numero_cuenta')
                            ->label('Número de Cuenta (Opcional)')
                            ->placeholder('Ej: 191-12345678-0-99')
                            ->default('N/A'),

                        TextInput::make('cci')
                            ->label('Código Interbancario CCI (Opcional)')
                            ->placeholder('Ej: 002-191-0012345678099-50')
                            ->default('N/A'),

                        TextInput::make('titular')
                            ->label('Titular de la Cuenta / Razón Social')
                            ->placeholder('Ej: Junta de Propietarios Jorge Chávez'),

                        Select::make('tipo_cuenta')
                            ->label('Tipo de Cuenta')
                            ->options([
                                'Corriente' => 'Cuenta Corriente',
                                'Ahorros' => 'Cuenta de Ahorros',
                            ])
                            ->default('Corriente'),

                        TextInput::make('saldo_inicial')
                            ->label('Saldo Inicial S/')
                            ->numeric()
                            ->prefix('S/')
                            ->default(0),
                    ])->columns(2),

                Section::make('📲 Cobro por Yape / Plin')
                    ->description('Configure los datos para cobros inmediatos por código QR.')
                    ->schema([
                        Toggle::make('activo_yape_plin')
                            ->label('Habilitar cobro por Yape / Plin')
                            ->default(true)
                            ->reactive(),

                        Group::make([
                            TextInput::make('yape_plin_numero')
                                ->label('Número de Yape / Plin')
                                ->placeholder('Ej: 987654321'),

                            TextInput::make('yape_plin_titular')
                                ->label('Titular de Yape / Plin')
                                ->placeholder('Ej: Junta Jorge Chávez'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('activo_yape_plin')),
                    ]),

                Section::make('💳 Pasarela de Pago con Tarjeta de Crédito / Débito (Web y App Móvil)')
                    ->description('Active el cobro con tarjeta para la Web de Vecinos y la App Móvil.')
                    ->schema([
                        Toggle::make('activo_tarjeta')
                            ->label('HABILITAR PAGO CON TARJETA DE CRÉDITO Y DÉBITO')
                            ->default(false)
                            ->reactive(),

                        Group::make([
                            Select::make('pasarela_proveedor')
                                ->label('Proveedor de Pasarela')
                                ->options([
                                    'niubiz' => 'Niubiz (Visa / Mastercard)',
                                    'izipay' => 'Izipay Perú',
                                    'culqi' => 'Culqi',
                                    'mercadopago' => 'Mercado Pago Perú',
                                ])
                                ->default('niubiz'),

                            Select::make('pasarela_entorno')
                                ->label('Entorno de Trabajo')
                                ->options([
                                    'sandbox' => 'Pruebas / Sandbox',
                                    'production' => 'Producción / En Vivo (Live)',
                                ])
                                ->default('production'),

                            TextInput::make('pasarela_merchant_id')
                                ->label('Código de Comercio / Merchant ID')
                                ->placeholder('Ej: 456892301'),

                            TextInput::make('pasarela_public_key')
                                ->label('Clave Pública / Public Key')
                                ->placeholder('Ej: pk_live_xxxxxxxxxxxx'),

                            TextInput::make('pasarela_secret_key')
                                ->label('Clave Privada / Secret Key')
                                ->password()
                                ->revealable()
                                ->placeholder('Ej: sk_live_xxxxxxxxxxxx'),
                        ])
                        ->columns(2)
                        ->visible(fn ($get) => $get('activo_tarjeta')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_banco')
                    ->label('BANCO')
                    ->badge()
                    ->color(fn ($record) => ($record->activo_yape_plin && ($record->numero_cuenta === 'N/A' || empty($record->numero_cuenta))) ? 'success' : 'primary')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->activo_yape_plin && ($record->numero_cuenta === 'N/A' || empty($record->numero_cuenta))) {
                            return '📲 Yape / Plin';
                        }
                        return $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('numero_cuenta')
                    ->label('Nº CUENTA')
                    ->formatStateUsing(function ($state, $record) {
                        if (!empty($record->yape_plin_numero) && ($state === 'N/A' || empty($state))) {
                            return '📲 ' . $record->yape_plin_numero;
                        }
                        return $state ?? 'N/A';
                    }),

                Tables\Columns\TextColumn::make('tipo_cuenta')
                    ->label('TIPO')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->activo_yape_plin && ($record->numero_cuenta === 'N/A' || empty($record->numero_cuenta))) {
                            return 'Billetera Digital';
                        }
                        return $state ?? 'Corriente';
                    }),

                Tables\Columns\IconColumn::make('activo_yape_plin')->label('YAPE/PLIN')->boolean(),
                Tables\Columns\IconColumn::make('activo_tarjeta')->label('TARJETA')->boolean(),
                Tables\Columns\TextColumn::make('saldo_inicial')->label('SALDO INICIAL')->money('PEN'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBancos::route('/'),
            'create' => Pages\CreateBanco::route('/create'),
            'edit' => Pages\EditBanco::route('/{record}/edit'),
        ];
    }
}