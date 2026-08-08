<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;
    protected static bool $isScopedToTenant = false;

    // RESTRICCIÓN: Solo Admin
   public static function canViewAny(): bool {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'admin']);
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Directorio de Proveedores';
    protected static ?string $navigationGroup = 'Mantenimiento & Equipos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la Empresa/Técnico')
                ->schema([
                    Forms\Components\TextInput::make('nombre_empresa')->label('Nombre / Razón Social')->required(),
                    Forms\Components\TextInput::make('rubro')->label('Rubro (Ej: Gasfitería, Ascensores)')->required(),
                    Forms\Components\TextInput::make('contacto_nombre')->label('Persona de contacto'),
                    Forms\Components\TextInput::make('telefono')->label('Teléfono de Emergencia')->tel()->required(),
                    Forms\Components\TextInput::make('email')->label('Correo Electrónico')->email(),
                    Forms\Components\TextInput::make('ruc')->label('RUC'),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nombre_empresa')->label('Proveedor')->searchable(),
            Tables\Columns\TextColumn::make('rubro')->label('Especialidad')->badge(),
            Tables\Columns\TextColumn::make('telefono')->label('Teléfono'),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array {
        return ['index' => Pages\ListProveedors::route('/'), 'create' => Pages\CreateProveedor::route('/create'), 'edit' => Pages\EditProveedor::route('/{record}/edit')];
    }
}