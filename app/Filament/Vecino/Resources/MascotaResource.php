<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Mascota;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MascotaResource extends Resource
{
    protected static ?string $model = Mascota::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Mis Mascotas';
    protected static ?string $pluralModelLabel = 'Registro de Mascotas';
    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('departamento_id', auth()->user()->departamento_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registrar Nueva Mascota')
                    ->description('Ingresa los datos de tu mascota para el padrón del edificio.')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la Mascota')
                            ->required()
                            ->placeholder('Ej: Firulais'),

                        Forms\Components\Select::make('especie')
                            ->label('Especie')
                            ->options([
                                'Perro' => '🐶 Perro',
                                'Gato' => '🐱 Gato',
                                'Ave' => '🦜 Ave',
                                'Otro' => '🐾 Otro',
                            ])
                            ->default('Perro')
                            ->required(),

                        Forms\Components\TextInput::make('raza')
                            ->label('Raza / Descripción')
                            ->placeholder('Ej: Poodle, Mestizo, Persa'),

                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto de la Mascota')
                            ->image()
                            ->disk('public')
                            ->directory('mascotas'),

                        Forms\Components\Hidden::make('departamento_id')
                            ->default(fn () => auth()->user()->departamento_id),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('especie')
                    ->label('Especie')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('raza')
                    ->label('Raza')
                    ->placeholder('Sin especificar'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MascotaResource\Pages\ListMascotas::route('/'),
        ];
    }
}