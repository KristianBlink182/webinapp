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

class BancoResource extends Resource
{
    protected static ?string $model = Banco::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Cuentas Bancarias';
    protected static ?string $modelLabel = 'Cuenta Bancaria';
    protected static ?string $pluralModelLabel = 'Cuentas Bancarias';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 4;
    

    public static function canViewAny(): bool { return auth()->user()->role === 'admin'; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Cuenta')
                    ->description('Registre las cuentas bancarias oficiales del edificio para la conciliación de pagos.')
                    ->schema([
                        Select::make('condominio_id')
                            ->label('Condominio Titular')
                            ->relationship('condominio', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(), // <--- ESTO EVITA EL ERROR QUE TE SALÍA

                        TextInput::make('nombre_banco')
                            ->label('Banco')
                            ->placeholder('Ej: BCP, BBVA, Interbank')
                            ->required(),

                        TextInput::make('numero_cuenta')
                            ->label('Número de Cuenta')
                            ->required(),

                        Select::make('tipo_cuenta')
                            ->label('Tipo de Cuenta')
                            ->options([
                                'Corriente' => 'Cuenta Corriente',
                                'Ahorros' => 'Cuenta de Ahorros',
                            ])->required(),

                        TextInput::make('saldo_inicial')
                            ->label('Saldo Inicial S/')
                            ->numeric()
                            ->prefix('S/')
                            ->default(0)
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('condominio.nombre')->label('Condominio'),
                Tables\Columns\TextColumn::make('nombre_banco')->label('Banco')->badge(),
                Tables\Columns\TextColumn::make('numero_cuenta')->label('N° Cuenta'),
                Tables\Columns\TextColumn::make('saldo_inicial')->label('Saldo Inicial')->money('PEN'),
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