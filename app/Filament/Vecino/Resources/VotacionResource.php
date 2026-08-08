<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Votacion;
use App\Models\Voto;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class VotacionResource extends Resource
{
    protected static ?string $model = Votacion::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Votaciones / Acuerdos';
    protected static ?string $pluralModelLabel = 'Votaciones y Acuerdos de la Directiva';
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('condominio_id', auth()->user()->departamento?->condominio_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto de Votación')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Cierre Votación')
                    ->date('d/m/Y'),

                // 🎯 INDICADOR SI EL VECINO YA VOTÓ O SI TIENE PENDIENTE SU VOTO
                Tables\Columns\TextColumn::make('mi_voto')
                    ->label('Tu Estado')
                    ->state(function (Votacion $record): string {
                        $voto = Voto::where('votacion_id', $record->id)->where('user_id', auth()->id())->first();
                        if ($voto) {
                            return '✅ Votaste: ' . $voto->opcion_seleccionada;
                        }
                        return '⏳ Pendiente Votar';
                    })
                    ->badge()
                    ->color(fn ($state) => str_contains($state, '✅') ? 'success' : 'warning'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // 🗳️ VENTANA POP-UP PARA QUE EL VECINO LEA EL SUSTENTO Y VOTE
                Tables\Actions\Action::make('votar')
                    ->label('🗳️ Ver Propuesta y Votar')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn (Votacion $record) => 'Consulta: ' . $record->titulo)
                    ->modalContent(function (Votacion $record) {
                        $descripcion = nl2br(e($record->descripcion ?? 'Sin descripción disponible.'));
                        $pdfBtn = '';

                        if (!empty($record->documento_adjunto)) {
                            $pdfUrl = asset('storage/' . $record->documento_adjunto);
                            $pdfBtn = "
                                <div style='margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #374151;'>
                                    <a href='{$pdfUrl}' target='_blank' style='display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: #0284c7; color: #ffffff; font-weight: 800; font-size: 0.85rem; border-radius: 0.75rem; text-decoration: none;'>
                                        📄 Ver / Descargar Cotización Adjunta (PDF)
                                    </a>
                                </div>
                            ";
                        }

                        return new HtmlString("
                            <div style='color: #e5e7eb; font-size: 0.9rem; line-height: 1.6; padding: 0.5rem 0;'>
                                <strong style='color: #ffffff; font-size: 1rem; display: block; margin-bottom: 0.5rem;'>Explicación de la Directiva:</strong>
                                <p style='color: #9ca3af;'>{$descripcion}</p>
                                {$pdfBtn}
                            </div>
                        ");
                    })
                    ->form(function (Votacion $record) {
                        $yaVoto = Voto::where('votacion_id', $record->id)->where('user_id', auth()->id())->exists();
                        if ($yaVoto) return [];

                        $opciones = is_array($record->opciones) ? $record->opciones : ['A Favor', 'En Contra', 'Abstención'];

                        return [
                            Forms\Components\Select::make('opcion')
                                ->label('Seleccione su Voto / Decisión')
                                ->options(array_combine($opciones, $opciones))
                                ->required(),
                        ];
                    })
                    ->action(function (Votacion $record, array $data) {
                        $yaVoto = Voto::where('votacion_id', $record->id)->where('user_id', auth()->id())->exists();
                        if ($yaVoto) return;

                        Voto::create([
                            'votacion_id'         => $record->id,
                            'user_id'             => auth()->id(),
                            'opcion_seleccionada' => $data['opcion'],
                        ]);

                        Notification::make()
                            ->title('¡Voto registrado con éxito!')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => VotacionResource\Pages\ListVotaciones::route('/'),
        ];
    }
}