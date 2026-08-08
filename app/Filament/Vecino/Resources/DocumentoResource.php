<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Documento;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Biblioteca de Documentos';
    protected static ?string $pluralModelLabel = 'Biblioteca de Documentos del Edificio';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

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
                    ->label('Nombre del Documento')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Publicación')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // 📥 BOTÓN NATIVO PARA VER Y DESCARGAR EL PDF EN PESTAÑA NUEVA
                Tables\Actions\Action::make('descargar')
                    ->label('Ver / Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->button()
                    ->url(fn (Documento $record) => asset('storage/' . $record->archivo))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DocumentoResource\Pages\ListDocumentos::route('/'),
        ];
    }
}