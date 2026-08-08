<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactoEmergenciaResource\Pages;
use App\Models\ContactoEmergencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactoEmergenciaResource extends Resource
{
    protected static ?string $model = ContactoEmergencia::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';
    protected static ?string $navigationLabel = 'Números de Emergencia';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Nuevo Contacto de Emergencia')
                ->schema([
                    Forms\Components\TextInput::make('nombre')->label('Institución o Técnico')->required(),
                    Forms\Components\TextInput::make('telefono')->label('Número telefónico')->tel()->required(),
                    Forms\Components\Select::make('categoria')
                        ->options([
                            'Seguridad' => '👮 Seguridad (Serenazgo/Policía)',
                            'Salud' => '🚑 Salud (Ambulancias)',
                            'Bomberos' => '🚒 Bomberos',
                            'Servicios' => '🛠️ Servicios Técnicos',
                        ])->required(),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('telefono')->label('Teléfono')->copyable(),
                Tables\Columns\TextColumn::make('categoria')->label('Categoría')->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('llamar')
                    ->label('Llamar')
                    ->icon('heroicon-m-phone')
                    ->color('success')
                    ->url(fn ($record) => "tel:{$record->telefono}"),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()->role === 'admin'),
            ]);
    }

    public static function getPages(): array {
        return ['index' => Pages\ListContactoEmergencias::route('/'), 'create' => Pages\CreateContactoEmergencia::route('/create')];
    }
}