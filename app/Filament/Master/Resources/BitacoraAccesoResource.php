<?php

namespace App\Filament\Master\Resources;

use App\Models\BitacoraAcceso;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BitacoraAccesoResource extends Resource
{
    protected static ?string $model = BitacoraAcceso::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Bitácora de Accesos';
    protected static ?string $pluralModelLabel = 'Auditoría de Inicios de Sesión';
    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Administrador / Usuario')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('condominio_nombre')
                    ->label('Edificio Consultado')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Dirección IP')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => BitacoraAccesoResource\Pages\ListBitacoraAccesos::route('/'),
        ];
    }
}