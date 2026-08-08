<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReclamoResource\Pages;
use App\Models\Reclamo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReclamoResource extends Resource
{
    protected static ?string $model = Reclamo::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Reclamos y Reportes';
    protected static ?string $pluralModelLabel = 'Atención de Reclamos';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Atención del Reclamo')
                    ->schema([
                        Forms\Components\TextInput::make('asunto')->disabled(),
                        Forms\Components\Textarea::make('descripcion')->label('Detalle del Vecino')->disabled(),
                        Forms\Components\Select::make('estado')
                            ->label('Estado de Atención')
                            ->options([
                                'Pendiente' => '🔴 Pendiente',
                                'En Proceso' => '🟡 En Proceso / En Revisión',
                                'Resuelto' => '🟢 Resuelto / Atendido',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departamento.numero')->label('Dpto')->badge()->color('info'),
                Tables\Columns\TextColumn::make('user.name')->label('Vecino'),
                Tables\Columns\TextColumn::make('asunto')->label('Asunto')->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y h:i A'),
                Tables\Columns\TextColumn::make('estado')->label('Estado')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Atender Reclamo'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReclamos::route('/'),
            'edit' => Pages\EditReclamo::route('/{record}/edit'),
        ];
    }
}