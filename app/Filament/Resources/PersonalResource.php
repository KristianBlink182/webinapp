<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalResource\Pages;
use App\Models\Personal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Mantenimiento & Equipos';
    protected static ?string $navigationLabel = 'Gestión de Personal';
    protected static ?string $pluralModelLabel = 'Personal del Edificio';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('condominio_id')
                    ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id),

                Forms\Components\Section::make('Datos del Empleado / Personal')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_completo')
                            ->label('Nombre Completo')
                            ->required()
                            ->placeholder('Ej: Pedro Gutierrez'),

                        Forms\Components\TextInput::make('dni')
                            ->label('DNI / Documento')
                            ->placeholder('09876543'),

                        Forms\Components\Select::make('puesto')
                            ->label('Cargo / Puesto')
                            ->options([
                                'Vigilante' => '🛡️ Vigilante / Portero',
                                'Limpieza' => '🧹 Personal de Limpieza',
                                'Jardinero' => '🪴 Jardinero',
                                'Mantenimiento' => '🛠️ Técnico de Mantenimiento',
                                'Administrador' => '💼 Asistente Administrativo',
                                'Otro' => '👤 Otro',
                            ])
                            ->default('Vigilante')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('turno')
                            ->label('Turno / Horario de Trabajo')
                            ->options([
                                'Mañana' => '🌅 Turno Mañana',
                                'Tarde' => '🌤️ Turno Tarde',
                                'Noche' => '🌙 Turno Noche / Nocturno',
                            ])
                            ->default('Mañana')
                            ->required(),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono de Contacto')
                            ->tel()
                            ->placeholder('+51 987654321'),

                        Forms\Components\Select::make('estado')
                            ->label('Estado Laboral')
                            ->options([
                                'Activo' => '🟢 Activo (Trabajando)',
                                'Inactivo' => '🔴 Inactivo / Cesado',
                            ])
                            ->default('Activo')
                            ->required(),
                    ])->columns(2),

                // SECCIÓN DE ACCESO PARA VIGILANTES Y PORTERÍA
                Forms\Components\Section::make('🔐 Acceso al Panel de Portería (/porteria)')
                    ->description('Asigne correo y contraseña para que el vigilante ingrese al sistema.')
                    ->visible(fn ($get) => $get('puesto') === 'Vigilante')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico para Iniciar Sesión')
                            ->email()
                            ->required(fn ($get) => $get('puesto') === 'Vigilante')
                            ->placeholder('vigilancia@edificio.com'),

                        Forms\Components\TextInput::make('password_acceso')
                            ->label('Contraseña para el Panel de Portería')
                            ->password()
                            ->dehydrated(false)
                            ->placeholder('Escriba una contraseña (Ej: 123456)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Empleado')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('puesto')
                    ->label('Cargo')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email de Acceso')
                    ->placeholder('Sin usuario'),

                Tables\Columns\TextColumn::make('turno')
                    ->label('Turno')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Inactivo' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonals::route('/'),
            'create' => Pages\CreatePersonal::route('/create'),
            'edit' => Pages\EditPersonal::route('/{record}/edit'),
        ];
    }
}