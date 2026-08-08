<?php

namespace App\Filament\Master\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Gestión de Usuarios del SaaS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña de Acceso')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Forms\Components\Select::make('role')
                            ->label('Rol asignado')
                            ->options([
                                'superadmin' => 'Superadmin (Equipo SaaS)',
                                'admin' => 'Administrador de Edificio',
                                'residente' => 'Residente / Vecino',
                                'vigilante' => 'Portería / Vigilante',
                            ])
                            ->required()
                            ->reactive(),

                        // 🎯 SELECCIONAR QUÉ EDIFICIO VA A ADMINISTRAR ESTE USUARIO
                        Forms\Components\Select::make('condominios')
                            ->label('Edificio / Condominio que Administra')
                            ->relationship('condominios', 'nombre')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn ($get) => in_array($get('role'), ['admin', 'vigilante'])),

                        // SI ES RESIDENTE, SELECCIONAR SU DEPARTAMENTO
                        Forms\Components\Select::make('departamento_id')
                            ->label('Departamento Asignado')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->nullable()
                            ->visible(fn ($get) => $get('role') === 'residente'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin' => 'warning',
                        'residente' => 'success',
                        'vigilante' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('edificio_nombre')
                    ->label('Edificio / Condominio')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(function (User $record): string {
                        if ($record->role === 'superadmin') return 'Global LIVO';
                        
                        if ($record->departamento?->condominio) {
                            return $record->departamento->condominio->nombre;
                        }

                        if ($record->condominios->isNotEmpty()) {
                            return $record->condominios->pluck('nombre')->join(', ');
                        }

                        return 'Sin asignar';
                    }),

                Tables\Columns\TextColumn::make('departamento.numero')
                    ->label('Dpto')
                    ->placeholder('N/A'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => UserResource\Pages\ListUsers::route('/'),
        ];
    }
}