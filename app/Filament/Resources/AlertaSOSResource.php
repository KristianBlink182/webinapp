<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertaSOSResource\Pages;
use App\Models\AlertaSOS;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class AlertaSOSResource extends Resource
{
    protected static ?string $model = AlertaSOS::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Alerta S O S';
    protected static ?string $navigationGroup = 'Seguridad';

    public static function canViewAny(): bool { return true; }

    // BOTÓN DE CREAR: Solo visible para Residentes
    public static function canCreate(): bool 
    { 
        return auth()->user()->role === 'residente'; 
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('¿Cuál es su emergencia?')
                ->description('Al enviar, daremos aviso inmediato a portería.')
                ->schema([
                    Placeholder::make('advertencia')
                        ->label('')
                        ->content(new HtmlString('
                            <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; border: 1px solid #fecaca;">
                                <strong>🚨 ¡ATENCIÓN!</strong> Al presionar crear, se enviará su nombre y departamento al vigilante. Use esto solo en casos de peligro real.
                            </div>
                        ')),
                    Select::make('tipo_emergencia')
                        ->label('Tipo de Emergencia')
                        ->options([
                            'Seguridad' => 'Seguridad (Robo/Intruso)',
                            'Medica' => 'Médica (Salud/Accidente)',
                            'Incendio' => 'Incendio',
                        ])->required(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s') // Refresco automático
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Hora')
                    ->dateTime('H:i:s')
                    ->color('danger')
                    ->weight('bold'),
                
                // Solo el Admin ve el nombre del vecino en la tabla general
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Vecino')
                    ->visible(fn() => auth()->user()->role !== 'residente'),

                Tables\Columns\TextColumn::make('user.departamento.numero')
                    ->label('Dep.')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('atendido')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'ATENDIDO' : 'PENDIENTE')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ])
            ->actions([
                // BOTÓN DE ATENDER: Solo visible para Admin y Vigilante
                Tables\Actions\Action::make('atender')
                    ->label('Marcar Atendido')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (AlertaSOS $record) => !$record->atendido && auth()->user()->role !== 'residente')
                    ->action(fn (AlertaSOS $record) => $record->update(['atendido' => true])),
            ]);
    }

    // FILTRO DE SEGURIDAD: El vecino solo ve sus SOS. El Admin ve todos.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role === 'residente') {
            $query->where('user_id', auth()->id());
        }
        return $query;
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListAlertaSOS::route('/'),
            'create' => Pages\CreateAlertaSOS::route('/create'),
        ];
    }
}