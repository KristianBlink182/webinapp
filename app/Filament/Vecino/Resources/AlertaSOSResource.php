<?php

namespace App\Filament\Vecino\Resources;

use App\Models\AlertaSOS;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlertaSOSResource extends Resource
{
    protected static ?string $model = AlertaSOS::class;

    protected static bool $isScopedToTenant = false;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $navigationLabel = 'Alerta S O S';
    protected static ?string $pluralModelLabel = 'Alertas S O S de Emergencia';
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($q) {
                $q->where('departamento_id', auth()->user()->departamento_id)
                  ->orWhere('user_id', auth()->id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Emitir Alerta de Emergencia S.O.S')
                    ->description('Se notificará de inmediato a la Portería y a la Administración.')
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Emergencia')
                            ->options([
                                'Medica'    => '🚑 Emergencia Médica',
                                'Seguridad' => '👮 Seguridad / Inseguridad',
                                'Incendio'  => '🔥 Incendio',
                                'Ascensor'  => '🛗 Persona Atrapada en Ascensor',
                                'Otro'      => '⚠️ Otra Emergencia',
                            ])
                            ->default('Medica')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Detalles / Observaciones')
                            ->placeholder('Detalla brevemente la situación...')
                            ->rows(3)
                            ->columnSpanFull(),

                        // 🔊 SUBIDA / GRABACIÓN DE AUDIO DE VOZ
                        Forms\Components\FileUpload::make('audio_path')
                            ->label('Audio de Voz de Emergencia (Opcional)')
                            ->disk('public')
                            ->directory('audios_sos')
                            ->acceptedFileTypes(['audio/*'])
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        Forms\Components\Hidden::make('departamento_id')
                            ->default(fn () => auth()->user()->departamento_id),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => auth()->user()->departamento?->condominio_id),

                        Forms\Components\Hidden::make('estado')
                            ->default('Pendiente'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo de Emergencia')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha / Hora')
                    ->dateTime('d/m/y h:i A')
                    ->timezone('America/Lima')
                    ->sortable(),

                Tables\Columns\TextColumn::make('audio_path')
                    ->label('Audio de Voz')
                    ->badge()
                    ->formatStateUsing(fn ($record) => !empty($record->audio_path) ? '🔊 Audio Adjunto' : 'Sin Audio')
                    ->color(fn ($record) => !empty($record->audio_path) ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Pendiente' => 'danger',
                        'Atendido'  => 'success',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => AlertaSOSResource\Pages\ListAlertaSOS::route('/'),
        ];
    }
}