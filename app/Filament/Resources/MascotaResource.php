<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MascotaResource\Pages;
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
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Mis Mascotas';
    protected static ?string $navigationGroup = 'Comunidad';

    public static function canViewAny(): bool { return true; }
    public static function canCreate(): bool { return true; } // El vecino SÍ puede crear

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ficha de la Mascota')
                ->description('Registra a tu mascota para una mejor convivencia.')
                ->schema([
                    Forms\Components\TextInput::make('nombre')->label('Nombre de la mascota')->required(),
                    Forms\Components\Select::make('especie')->label('Especie')->options(['Perro'=>'Perro', 'Gato'=>'Gato', 'Otro'=>'Otro'])->required(),
                    Forms\Components\TextInput::make('raza')->label('Raza / Color'),
                    Forms\Components\FileUpload::make('foto')->image()->directory('mascotas')->disk('public'),
                    Forms\Components\Textarea::make('observaciones')->label('Notas importantes (ej: es nervioso)')->columnSpanFull(),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Mantén actualizado el registro de tus mascotas. Esto ayuda a la seguridad del edificio y a identificarlas si se pierden.')
            ->columns([
                Tables\Columns\ImageColumn::make('foto')->circular(),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('especie')->badge(),
                Tables\Columns\TextColumn::make('departamento.numero')->label('Departamento'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role === 'residente') {
            $query->where('departamento_id', auth()->user()->departamento_id);
        }
        return $query;
    }

    public static function getPages(): array {
        return ['index' => Pages\ListMascotas::route('/'), 'create' => Pages\CreateMascota::route('/create')];
    }
}