<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaComunResource\Pages;
use App\Models\AreaComun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AreaComunResource extends Resource
{
    protected static ?string $model = AreaComun::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Áreas Comunes';
    protected static ?string $pluralModelLabel = 'Gestión de Áreas Comunes';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getEloquentQuery(): Builder
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        return parent::getEloquentQuery()
            ->where(function ($query) use ($tenant) {
                if ($tenant) {
                    $query->where('condominio_id', $tenant->id);
                }
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('condominio_id')
                    ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->condominio_id),

                Forms\Components\Section::make('Configuración del Área')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del Área')
                            ->required()
                            ->placeholder('Ej: Zona de Parrilla N°1, SUM, Piscina'),

                        Forms\Components\TextInput::make('costo')
                            ->label('Costo S/')
                            ->prefix('S/')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('mesas')
                            ->label('Número de Mesas / Espacios')
                            ->numeric()
                            ->default(1),

                        Forms\Components\TextInput::make('capacidad_max')
                            ->label('Capacidad Max. Personas')
                            ->numeric()
                            ->default(10),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Disponible' => '🟢 Disponible',
                                'Mantenimiento' => '🔴 En Mantenimiento',
                            ])
                            ->default('Disponible')
                            ->required(),

                        Forms\Components\Textarea::make('reglas')
                            ->label('Reglas de Uso')
                            ->placeholder('Detalla las normas del área...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Área Común')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('costo')
                    ->label('Costo')
                    ->money('PEN')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('capacidad_max')
                    ->label('Aforo Max.')
                    ->suffix(' personas'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disponible' => 'success',
                        'Mantenimiento' => 'danger',
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
            'index' => Pages\ListAreaComuns::route('/'),
            'create' => Pages\CreateAreaComun::route('/create'),
            'edit' => Pages\EditAreaComun::route('/{record}/edit'),
        ];
    }
}