<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Reclamo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReclamoResource extends Resource
{
    protected static ?string $model = Reclamo::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Reclamos y Reportes';
    protected static ?string $pluralModelLabel = 'Reclamos y Sugerencias';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Enviar Reclamo o Reporte a la Directiva')
                    ->description('Detalla tu incidencia para que el Administrador lo atienda.')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Asunto / Título del Reclamo')
                            ->required()
                            ->placeholder('Ej: Ruidos molestos en el piso 4'),

                        Forms\Components\Select::make('prioridad')
                            ->label('Nivel de Urgencia')
                            ->options([
                                'Baja' => '🟢 Baja / Sugerencia',
                                'Media' => '🟡 Media / Atención Normal',
                                'Alta' => '🔴 Alta / Urgente',
                            ])
                            ->default('Media')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->placeholder('Explica brevemente los hechos, piso o área afectada...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        // 📸 CAMPO PARA ADJUNTAR FOTO DE EVIDENCIA
                        Forms\Components\FileUpload::make('foto')
                            ->label('Adjuntar Foto de Evidencia (Opcional)')
                            ->image()
                            ->disk('public')
                            ->directory('reclamos')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => auth()->user()->departamento?->condominio_id ?? \Filament\Facades\Filament::getTenant()?->id),

                        Forms\Components\Hidden::make('estado')
                            ->default('Pendiente'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Evidencia')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Urgencia')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Alta' => 'danger',
                        'Media' => 'warning',
                        'Baja' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'danger',
                        'En Proceso' => 'warning',
                        'Resuelto', 'Atendido' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver Detalle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReclamoResource\Pages\ListReclamos::route('/'),
        ];
    }
}