<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComunicadoResource\Pages;
use App\Models\Comunicado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class ComunicadoResource extends Resource
{
    protected static ?string $model = Comunicado::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Muro de Avisos';
    protected static ?string $modelLabel = 'Comunicado';
    protected static ?string $pluralModelLabel = 'Comunicados';
    protected static ?string $navigationGroup = 'Comunidad';

    public static function canViewAny(): bool
    {
        $role = strtolower(auth()->user()->role ?? '');
        return in_array($role, ['admin', 'administrador', 'super_admin', 'master', 'residente']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detalle del Comunicado')
                ->description('Aviso o comunicado oficial para los vecinos del edificio.')
                ->schema([
                    Select::make('condominio_id')
                        ->label('Condominio')
                        ->relationship('condominio', 'nombre')
                        ->default(fn () => \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->departamento?->condominio_id)
                        ->searchable()
                        ->required(),

                    Select::make('tipo')
                        ->label('Tipo de Aviso')
                        ->options([
                            'Información'  => 'ℹ️ Información General',
                            'Urgente'      => '🚨 Urgente / Importante',
                            'Mantenimiento'=> '🔧 Mantenimiento',
                        ])
                        ->default('Información')
                        ->required(),

                    TextInput::make('titulo')
                        ->label('Título del mensaje')
                        ->placeholder('Ej: Corte de Luz Programado')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('contenido')
                        ->label('Descripción Completa')
                        ->placeholder('Escriba aquí todo el detalle del aviso...')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),

                    // 📢 ADJUNTAR AFICHE / FOTO / PDF
                    FileUpload::make('imagen_adjunto')
                        ->label('Adjuntar Volante / Foto / PDF (Opcional)')
                        ->disk('public')
                        ->directory('comunicados')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->columnSpanFull(),

                    DatePicker::make('fecha_publicacion')
                        ->label('Fecha de publicación')
                        ->default(now())
                        ->required(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('condominio.nombre')
                    ->label('Edificio'),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->weight('bold')
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
                Tables\Actions\ViewAction::make()->label('Leer aviso'),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListComunicados::route('/'),
            'create' => Pages\CreateComunicado::route('/create'),
            'edit'   => Pages\EditComunicado::route('/{record}/edit'),
        ];
    }
}