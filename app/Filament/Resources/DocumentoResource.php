<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoResource\Pages;
use App\Models\Documento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;

    // 🎯 REGLA PRINCIPAL LIVO: DESACTIVAR EL SCOPING AUTOMÁTICO DE FILAMENT
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Biblioteca de Documentos';
    protected static ?string $modelLabel = 'Documento';
    protected static ?string $pluralModelLabel = 'Documentos';
    protected static ?string $navigationGroup = 'Comunidad';

    public static function canViewAny(): bool { return true; }

    // El vecino solo lee, el admin sube
    public static function canCreate(): bool 
    { 
        $role = strtolower(auth()->user()->role ?? '');
        return in_array($role, ['admin', 'administrador', 'super_admin', 'master']); 
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Subir Documento Informativo')
                ->schema([
                    Select::make('condominio_id')
                        ->label('Condominio')
                        ->relationship('condominio', 'nombre')
                        ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->departamento?->condominio_id)
                        ->required(),

                    TextInput::make('titulo')
                        ->label('Título del documento')
                        ->required()
                        ->placeholder('Ej: Reglamento Interno 2026'),

                    FileUpload::make('archivo')
                        ->label('Archivo PDF / Documento')
                        ->directory('documentos')
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Aquí puedes consultar y descargar todos los reglamentos oficiales, actas de reuniones y manuales de tu edificio.')
            ->columns([
                Tables\Columns\TextColumn::make('condominio.nombre')
                    ->label('Condominio'),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Nombre del Documento')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de carga')
                    ->date('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('descargar')
                    ->label('Ver / Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (Documento $record) => asset('storage/' . $record->archivo))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $query = parent::getEloquentQuery();

        if ($tenant) {
            $query->where('condominio_id', $tenant->id);
        } elseif (auth()->user()->departamento?->condominio_id) {
            $query->where('condominio_id', auth()->user()->departamento->condominio_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentos::route('/'),
            'create' => Pages\CreateDocumento::route('/create'),
        ];
    }
}