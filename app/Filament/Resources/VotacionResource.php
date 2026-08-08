<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VotacionResource\Pages;
use App\Models\Votacion;
use App\Models\Voto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TagsInput;
use Illuminate\Support\HtmlString;

class VotacionResource extends Resource
{
    protected static ?string $model = Votacion::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationLabel = 'Votaciones / Acuerdos';
    protected static ?string $pluralModelLabel = 'Votaciones y Consultas';
    protected static ?string $navigationGroup = 'Comunidad';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detalle de la Consulta o Propuesta')
                ->schema([
                    Forms\Components\TextInput::make('titulo')
                        ->label('Asunto / Título de la Votación')
                        ->placeholder('Ej: Compra e Instalación de Bomba Eléctrica')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Detalles / Sustento de la Propuesta')
                        ->placeholder('Explica los motivos, proforma seleccionada o beneficios para el edificio...')
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('documento_adjunto')
                        ->label('Adjuntar Cotización / Proforma / PDF (Opcional)')
                        ->disk('public')
                        ->directory('votaciones')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('condominio_id')
                        ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->departamento?->condominio_id),
                ]),

            Forms\Components\Section::make('Configuración de Opciones y Plazo')
                ->schema([
                    TagsInput::make('opciones')
                        ->label('Opciones de Respuesta')
                        ->default(['A Favor', 'En Contra', 'Abstención'])
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_limite')
                        ->label('Cierre de Votación')
                        ->minDate(now())
                        ->default(now()->addDays(7))
                        ->required(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto')
                    ->weight('extrabold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('esta_activa')
                    ->label('Activa')
                    ->boolean(),

                Tables\Columns\TextColumn::make('votos_count')
                    ->counts('votos')
                    ->label('Total Votos')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // 📊 BOTÓN MORADO PARA VER GRÁFICOS Y RESULTADOS EN TIEMPO REAL
                Tables\Actions\Action::make('verResultados')
                    ->label('Ver Resultados')
                    ->icon('heroicon-m-chart-bar')
                    ->color('purple')
                    ->button()
                    ->modalHeading(fn (Votacion $record) => 'Resultados: ' . $record->titulo)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (Votacion $record) {
                        $votos = Voto::where('votacion_id', $record->id)->with('user')->get();
                        $totalVotos = $votos->count();

                        $conteoPorOpcion = [];
                        $opciones = is_array($record->opciones) ? $record->opciones : ['A Favor', 'En Contra', 'Abstención'];

                        foreach ($opciones as $op) {
                            $conteoPorOpcion[$op] = $votos->where('opcion_seleccionada', $op)->count();
                        }

                        $htmlResultados = '';
                        foreach ($conteoPorOpcion as $opcion => $cantidad) {
                            $porcentaje = $totalVotos > 0 ? round(($cantidad / $totalVotos) * 100) : 0;
                            $htmlResultados .= "
                                <div style='margin-bottom: 1rem;'>
                                    <div style='display: flex; justify-content: space-between; font-weight: 700; color: #ffffff; font-size: 0.85rem; margin-bottom: 0.25rem;'>
                                        <span>{$opcion}</span>
                                        <span>{$cantidad} voto(s) ({$porcentaje}%)</span>
                                    </div>
                                    <div style='width: 100%; background: #374151; height: 12px; border-radius: 9999px; overflow: hidden;'>
                                        <div style='width: {$porcentaje}%; background: #38bdf8; height: 100%; border-radius: 9999px;'></div>
                                    </div>
                                </div>
                            ";
                        }

                        $htmlDetalle = "<div style='margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #374151;'><h4 style='color: #ffffff; font-weight: 800; margin-bottom: 0.75rem; font-size: 0.9rem;'>Detalle por Departamento:</h4>";
                        if ($totalVotos > 0) {
                            foreach ($votos as $v) {
                                $dptoNum = $v->user?->departamento?->numero ?? 'N/A';
                                $htmlDetalle .= "
                                    <div style='display: flex; justify-content: space-between; padding: 0.5rem 0.75rem; background: #1f2937; border-radius: 0.5rem; margin-bottom: 0.35rem; font-size: 0.8rem; color: #e5e7eb;'>
                                        <span><strong>{$v->user?->name}</strong> (Dpto. {$dptoNum})</span>
                                        <span style='font-weight: 800; color: #38bdf8;'>{$v->opcion_seleccionada}</span>
                                    </div>
                                ";
                            }
                        } else {
                            $htmlDetalle .= "<p style='color: #9ca3af; font-size: 0.8rem;'>Aún no hay votos registrados.</p>";
                        }
                        $htmlDetalle .= "</div>";

                        return new HtmlString("<div style='padding: 0.5rem 0;'>" . $htmlResultados . $htmlDetalle . "</div>");
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVotacions::route('/'),
            'create' => Pages\CreateVotacion::route('/create'),
            'edit'   => Pages\EditVotacion::route('/{record}/edit'),
        ];
    }
}