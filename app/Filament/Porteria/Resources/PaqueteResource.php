<?php

namespace App\Filament\Porteria\Resources;

use App\Models\Paquete;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaqueteResource extends Resource
{
    protected static ?string $model = Paquete::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Paquetes / Delivery';
    protected static ?string $pluralModelLabel = 'Recepción de Paquetes';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Recepción Rápida de Encomienda')
                    ->description('Selecciona el departamento y toma la foto del paquete.')
                    ->schema([
                        // 1. DEPARTAMENTO
                        Forms\Components\Select::make('departamento_id')
                            ->label('Departamento Destino')
                            ->relationship('departamento', 'numero')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $depa = Departamento::find($state);
                                    $nombre = $depa?->nombre_propietario ?? $depa?->nombre_inquilino ?? 'Residente';
                                    $set('destinatario', $nombre);
                                }
                            }),

                        // 2. FOTO DEL PAQUETE
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto del Paquete / Caja')
                            ->image()
                            ->disk('public')
                            ->directory('paquetes')
                            ->required(),

                        // CAMPOS AUTOMÁTICOS EN SEGUNDO PLANO (SIN PEDIR TEXTO AL PORTERO)
                        Forms\Components\Hidden::make('destinatario')
                            ->default('Residente'),

                        Forms\Components\Hidden::make('empresa_envio')
                            ->default('Delivery / Encomienda'),

                        Forms\Components\Hidden::make('estado')
                            ->default('En Recepción'),

                        Forms\Components\Hidden::make('fecha_recibido')
                            ->default(fn () => now('America/Lima')),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->departamento?->condominio_id),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('departamento.numero')
                    ->label('Dpto Destino')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('destinatario')
                    ->label('Destinatario')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_recibido')
                    ->label('Hora Notificación')
                    ->dateTime('d/m/y h:i A')
                    ->timezone('America/Lima')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'En Recepción' => 'warning',
                        'Entregado'    => 'success',
                        default        => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('marcarEntregado')
                    ->label('Entregar a Vecino')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (Paquete $record) => $record->estado === 'En Recepción')
                    ->action(fn (Paquete $record) => $record->update([
                        'estado'          => 'Entregado',
                        'fecha_entregado' => now('America/Lima'),
                    ])),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PaqueteResource\Pages\ListPaquetes::route('/'),
        ];
    }
}