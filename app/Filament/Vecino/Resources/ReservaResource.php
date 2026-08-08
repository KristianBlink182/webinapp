<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Reserva;
use App\Models\AreaComun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Gestión de Espacios';
    protected static ?string $navigationLabel = 'Áreas Comunes';
    protected static ?string $pluralModelLabel = 'Mis Reservas de Áreas Comunes';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->departamento_id) {
                    $query->orWhere('departamento_id', $user->departamento_id);
                }
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Solicitar Reserva de Área Común')
                    ->description('Selecciona el área común (Parrilla, SUM, Piscina, etc.), horario y adjunta tu voucher si la zona lo requiere.')
                    ->schema([
                        Forms\Components\Select::make('area_comun_id')
                            ->label('Área Común')
                            ->options(function () {
                                $condoId = auth()->user()->departamento?->condominio_id ?? \Filament\Facades\Filament::getTenant()?->id;
                                return AreaComun::where('condominio_id', $condoId)->pluck('nombre', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha de Uso')
                            ->minDate(now()->startOfDay())
                            ->default(now())
                            ->required(),

                        Forms\Components\TimePicker::make('hora_inicio')
                            ->label('Hora de Inicio')
                            ->seconds(false)
                            ->minutesStep(15)
                            ->required(),

                        Forms\Components\TimePicker::make('hora_fin')
                            ->label('Hora de Fin')
                            ->seconds(false)
                            ->minutesStep(15)
                            ->required()
                            ->after('hora_inicio'),

                        // 💳 SUBIR VOUCHER DE PAGO
                        Forms\Components\FileUpload::make('voucher')
                            ->label('Adjuntar Voucher de Pago (Para Parrilla / Zonas con costo)')
                            ->image()
                            ->disk('public')
                            ->directory('vouchers_reservas')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('comentario')
                            ->label('Nota / Motivo de la Reserva')
                            ->placeholder('Ej: Cumpleaños familiar, reunión con amigos...')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        Forms\Components\Hidden::make('departamento_id')
                            ->default(fn () => auth()->user()->departamento_id ?? auth()->user()->departamento?->id),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => auth()->user()->departamento?->condominio_id ?? \Filament\Facades\Filament::getTenant()?->id),

                        Forms\Components\Hidden::make('estado')
                            ->default('Pendiente'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areaComun.nombre')
                    ->label('Área Común')
                    ->weight('extrabold')
                    ->icon('heroicon-m-building-office-2')
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-calendar')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Horario de Uso')
                    ->icon('heroicon-m-clock')
                    ->formatStateUsing(fn ($record) => date('h:i A', strtotime($record->hora_inicio)) . ' - ' . date('h:i A', strtotime($record->hora_fin))),

                Tables\Columns\ImageColumn::make('voucher')
                    ->label('Voucher')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Aprobada'  => 'success',
                        'Pendiente' => 'warning',
                        'Cancelada' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Cancelar Reserva'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReservaResource\Pages\ListReservas::route('/'),
        ];
    }
}