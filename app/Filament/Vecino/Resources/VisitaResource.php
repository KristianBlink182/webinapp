<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitaResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $navigationLabel = 'Mis Invitados';
    protected static ?string $pluralModelLabel = 'Pre-Autorización de Invitados';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('departamento_id', auth()->user()->departamento_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pre-Autorizar Invitado o Visitante')
                    ->description('Al registrar a tu invitado, la Portería le dará acceso inmediato al llegar.')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_visitante')
                            ->label('Nombre del Invitado')
                            ->required()
                            ->placeholder('Ej: Maria Fernandez'),

                        Forms\Components\TextInput::make('dni_visitante')
                            ->label('DNI / Documento del Invitado')
                            ->placeholder('45982103'),

                        Forms\Components\Select::make('motivo')
                            ->label('Tipo de Visita')
                            ->options([
                                'Familiar' => 'Familiar / Amigo',
                                'Delivery' => 'Delivery / Encomienda',
                                'Tecnico'  => 'Técnico / Servicio',
                            ])
                            ->default('Familiar')
                            ->required(),

                        Forms\Components\Hidden::make('departamento_id')
                            ->default(fn () => auth()->user()->departamento_id),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => auth()->user()->departamento?->condominio_id),

                        Forms\Components\Hidden::make('estado_visita')
                            ->default('Programado'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_visitante')
                    ->label('Invitado')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dni_visitante')
                    ->label('DNI')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('motivo')
                    ->label('Tipo')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Anunciado')
                    ->dateTime('d/m/y H:i A')
                    ->timezone('America/Lima')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado_visita')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Programado' => 'info',
                        'Dentro'     => 'success',
                        'Retirado'   => 'gray',
                        default      => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Cancelar Pase'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => VisitaResource\Pages\ListVisitas::route('/'),
        ];
    }
}