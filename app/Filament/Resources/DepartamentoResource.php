<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Estructura del Edificio';
    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $pluralModelLabel = 'Padrón de Departamentos';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('condominio_id')
                ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id ?? 1),

            Forms\Components\Section::make('Ubicación y Participación')
                ->schema([
                    Forms\Components\TextInput::make('numero')
                        ->label('Número de Dpto / Casa')
                        ->placeholder('Ej: 101, 202, 1001')
                        ->required()
                        // REGLA STRICTA: PROHIBIR DUPLICAR EL MISMO DEPARTAMENTO EN EL EDIFICIO
                        ->unique('departamentos', 'numero', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                            $condoId = \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id ?? 1;
                            return $rule->where('condominio_id', $condoId);
                        })
                        ->validationMessages([
                            'unique' => 'El número de departamento o casa ya existe en este edificio.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!empty($state) && is_numeric($state)) {
                                $pisoCalculado = (int) ($state / 100);
                                if ($pisoCalculado > 0) {
                                    $set('piso', $pisoCalculado);
                                }
                            }
                        }),

                    Forms\Components\TextInput::make('piso')
                        ->label('Piso')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Forms\Components\TextInput::make('porcentaje_participacion')
                        ->label('% de Participación (Prorrateo)')
                        ->suffix('%')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Forms\Components\TextInput::make('estacionamiento')
                        ->label('Estacionamiento / Cochera')
                        ->placeholder('Ej: Cochera 12'),
                ])->columns(2),

            Forms\Components\Section::make('Datos del Propietario (Titular Legal)')
                ->description('El Propietario es el responsable legal directo de los pagos y multas del inmueble.')
                ->schema([
                    Forms\Components\TextInput::make('nombre_propietario')
                        ->label('Nombre Completo del Propietario')
                        ->required()
                        ->placeholder('Ej: Carlos Alberto Benavides'),

                    Forms\Components\TextInput::make('telefono_propietario')
                        ->label('Celular / Whatsapp del Propietario')
                        ->tel()
                        ->placeholder('+51 987654321'),

                    Forms\Components\TextInput::make('email_propietario')
                        ->label('Correo Electrónico del Propietario')
                        ->email(),

                    Forms\Components\Select::make('condicion')
                        ->label('Estado de Ocupación')
                        ->options([
                            'Habita el Propietario' => '🏠 Habita el Propietario',
                            'Alquilado' => '🏢 Alquilado a Inquilino',
                            'Desocupado' => '🚪 Desocupado',
                        ])
                        ->default('Habita el Propietario')
                        ->required()
                        ->reactive(),
                ])->columns(2),

            Forms\Components\Section::make('Datos del Inquilino / Arrendatario')
                ->description('Contacto de la persona que vive actualmente en el departamento.')
                ->visible(fn ($get) => $get('condicion') === 'Alquilado')
                ->schema([
                    Forms\Components\TextInput::make('nombre_inquilino')
                        ->label('Nombre Completo del Inquilino')
                        ->required(fn ($get) => $get('condicion') === 'Alquilado')
                        ->placeholder('Ej: Maria Jose Lopez'),

                    Forms\Components\TextInput::make('telefono_inquilino')
                        ->label('Celular / WhatsApp del Inquilino')
                        ->tel()
                        ->required(fn ($get) => $get('condicion') === 'Alquilado')
                        ->placeholder('+51 912345678'),

                    Forms\Components\TextInput::make('email_inquilino')
                        ->label('Correo del Inquilino')
                        ->email(),
                ])->columns(3),

            Forms\Components\Section::make('Acceso a la App del Vecino (/vecino)')
                ->description('Asigne una contraseña para que el residente ingrese a su panel con su correo electrónico.')
                ->schema([
                    Forms\Components\TextInput::make('password_acceso')
                        ->label('Contraseña para la App del Vecino')
                        ->password()
                        ->dehydrated(false)
                        ->placeholder('Escriba una contraseña (Ej: 123456)'),

                    Forms\Components\Placeholder::make('info_login')
                        ->label('Credenciales del Vecino')
                        ->content(fn ($record) => empty($record?->email_propietario)
                            ? new HtmlString('Ingrese el correo electrónico en la sección anterior.')
                            : new HtmlString('El usuario ingresará con el correo <strong>' . $record->email_propietario . '</strong>.')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('')
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Dpto')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('piso')
                    ->label('Piso')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre_propietario')
                    ->label('Propietario')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono_propietario')
                    ->label('Cel. Propietario'),

                Tables\Columns\TextColumn::make('condicion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state ?? '') {
                        'Habita el Propietario' => 'success',
                        'Alquilado' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('nombre_inquilino')
                    ->label('Inquilino Actual')
                    ->placeholder('—'),
            ])
            ->defaultSort('numero', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartamentos::route('/'),
            'create' => Pages\CreateDepartamento::route('/create'),
            'edit' => Pages\EditDepartamento::route('/{record}/edit'),
        ];
    }
}