<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoResource\Pages;
use App\Models\Mantenimiento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;

class MantenimientoResource extends Resource
{
    protected static ?string $model = Mantenimiento::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Mantenimiento de Equipos';
    protected static ?string $modelLabel = 'Mantenimiento';
    protected static ?string $navigationGroup = 'Mantenimiento & Equipos';

    public static function canViewAny(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalle del Equipo')
                    ->schema([
                        Select::make('condominio_id')
                            ->label('Condominio')
                            ->relationship('condominio', 'nombre')
                            ->required(),
                        TextInput::make('equipo')
                            ->label('Nombre del Equipo')
                            ->placeholder('Ej: Ascensor Schindler N°1')
                            ->required(),
                        DatePicker::make('ultima_fecha')
                            ->label('Última revisión')
                            ->required(),
                        DatePicker::make('proxima_fecha')
                            ->label('Próxima revisión programada')
                            ->required(),
                        TextInput::make('proveedor')
                            ->label('Empresa encargada'),
                        Forms\Components\Textarea::make('notas')
                            ->label('Observaciones técnicas')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('condominio.nombre')->label('Condominio'),
                TextColumn::make('equipo')->label('Equipo / Activo')->searchable(),
                TextColumn::make('proxima_fecha')
                    ->label('Estado / Próxima Fecha')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(fn (Mantenimiento $record): string => 
                        Carbon::parse($record->proxima_fecha)->isPast() ? 'danger' : (
                        Carbon::parse($record->proxima_fecha)->diffInDays(now()) < 7 ? 'warning' : 'success'
                        )
                    )
                    ->description(fn (Mantenimiento $record): string => 
                        Carbon::parse($record->proxima_fecha)->isPast() ? '¡VENCIDO!' : (
                        Carbon::parse($record->proxima_fecha)->diffInDays(now()) < 7 ? 'Vence pronto' : 'Al día'
                        )
                    ),
                TextColumn::make('proveedor')->label('Proveedor'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListMantenimientos::route('/'),
            'create' => Pages\CreateMantenimiento::route('/create'),
            'edit' => Pages\EditMantenimiento::route('/{record}/edit'),
        ];
    }
}