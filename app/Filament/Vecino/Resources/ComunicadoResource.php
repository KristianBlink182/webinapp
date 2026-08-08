<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Comunicado;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ComunicadoResource extends Resource
{
    protected static ?string $model = Comunicado::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Muro de Avisos';
    protected static ?string $pluralModelLabel = 'Muro de Avisos y Comunicados';
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

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
                    ->label('Título del Comunicado')
                    ->weight('extrabold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Nivel')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Urgente'       => 'danger',
                        'Mantenimiento' => 'warning',
                        default         => 'info',
                    }),

                Tables\Columns\TextColumn::make('fecha_publicacion')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // 📢 BOTÓN POP-UP PARA QUE EL VECINO LEA EL COMUNICADO COMPLETO Y VEA ADJUNTOS
                Tables\Actions\Action::make('leer')
                    ->label('📢 Leer Comunicado')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn (Comunicado $record) => $record->titulo)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (Comunicado $record) {
                        $contenido = nl2br(e($record->contenido ?? 'Sin contenido.'));
                        $adjuntoBtn = '';

                        if (!empty($record->imagen_adjunto)) {
                            $adjuntoUrl = asset('storage/' . $record->imagen_adjunto);
                            $adjuntoBtn = "
                                <div style='margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #374151;'>
                                    <a href='{$adjuntoUrl}' target='_blank' style='display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: #0284c7; color: #ffffff; font-weight: 800; font-size: 0.85rem; border-radius: 0.75rem; text-decoration: none;'>
                                        📄 Ver / Descargar Documento / Afiche Adjunto
                                    </a>
                                </div>
                            ";
                        }

                        return new HtmlString("
                            <div style='color: #e5e7eb; font-size: 0.95rem; line-height: 1.6; padding: 0.5rem 0;'>
                                <p style='color: #d1d5db; margin-bottom: 1rem;'>{$contenido}</p>
                                {$adjuntoBtn}
                            </div>
                        ");
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ComunicadoResource\Pages\ListComunicados::route('/'),
        ];
    }
}