<?php

namespace App\Filament\Vecino\Resources;

use App\Models\Anuncio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class AnuncioResource extends Resource
{
    protected static ?string $model = Anuncio::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?string $navigationLabel = 'Marketplace';
    protected static ?string $pluralModelLabel = 'Marketplace Vecinal';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationBadge(): ?string
    {
        $condoId = auth()->user()->departamento?->condominio_id;
        $count = Anuncio::where('condominio_id', $condoId)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('condominio_id', auth()->user()->departamento?->condominio_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Publicar Producto o Servicio')
                    ->description('Publica lo que vendes u ofreces a tus vecinos del condominio.')
                    ->schema([
                        Forms\Components\TextInput::make('producto')
                            ->label('Título del Producto / Servicio')
                            ->required()
                            ->placeholder('Ej: Secadora Portátil Eolic Ceramic'),

                        Forms\Components\TextInput::make('precio')
                            ->label('Precio (S/)')
                            ->prefix('S/')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('telefono_whatsapp')
                            ->label('Celular / WhatsApp de Contacto')
                            ->tel()
                            ->default(fn () => auth()->user()->telefono)
                            ->placeholder('Ej: 987654321')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción del Producto')
                            ->placeholder('Detalla el estado del producto...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('imagen')
                            ->label('Foto del Producto')
                            ->image()
                            ->disk('public')
                            ->directory('marketplace')
                            ->imageResizeMode('cover')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        Forms\Components\Hidden::make('condominio_id')
                            ->default(fn () => auth()->user()->departamento?->condominio_id),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detalle del Anuncio')
                    ->schema([
                        ImageEntry::make('imagen')
                            ->label('')
                            ->disk('public')
                            ->height(280)
                            ->columnSpanFull(),

                        TextEntry::make('producto')
                            ->label('Producto / Servicio')
                            ->weight('black')
                            ->size('lg'),

                        TextEntry::make('precio')
                            ->label('Precio')
                            ->money('PEN')
                            ->color('success')
                            ->weight('extrabold')
                            ->size('lg'),

                        TextEntry::make('user.name')
                            ->label('Vendedor')
                            ->formatStateUsing(fn ($record) => $record->user?->name . ' (Dpto. ' . ($record->user?->departamento?->numero ?? 'N/A') . ')'),

                        TextEntry::make('telefono_whatsapp')
                            ->label('WhatsApp de Contacto'),

                        TextEntry::make('descripcion')
                            ->label('Descripcion Completa')
                            ->columnSpanFull(),

                        Actions::make([
                            Action::make('whatsapp')
                                ->label('Contactar al Vendedor por WhatsApp')
                                ->icon('heroicon-m-chat-bubble-left-right')
                                ->color('success')
                                ->size('lg')
                                ->url(function (Anuncio $record) {
                                    $phone = preg_replace('/[^0-9]/', '', $record->telefono_whatsapp ?? $record->user?->telefono ?? '');
                                    if (strlen($phone) === 9) $phone = '51' . $phone;
                                    $msg = rawurlencode("Hola {$record->user?->name}, vi tu anuncio de '{$record->producto}' en LIVO y me interesa.");
                                    return "https://wa.me/{$phone}?text={$msg}";
                                })
                                ->openUrlInNewTab(),
                        ])->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    // 🎯 FIX DE IMAGEN COMPLETA EXPANDIDA
                  Tables\Columns\ImageColumn::make('imagen')
                            ->disk('public')
                            ->state(fn ($record) => !empty($record->imagen) ? "https://admin.livo.com.pe/storage/" . ltrim($record->imagen, '/') : null)
                            ->extraImgAttributes(['style' => 'width: 100%; height: 180px; object-fit: cover; border-radius: 1rem 1rem 0 0;']),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('producto')
                            ->weight('black')
                            ->size('lg')
                            ->searchable(),

                        Tables\Columns\TextColumn::make('precio')
                            ->money('PEN')
                            ->weight('extrabold')
                            ->color('success')
                            ->size('xl'),

                        Tables\Columns\TextColumn::make('user.name')
                            ->label('Vendedor')
                            ->formatStateUsing(fn ($record) => 'Vendedor: ' . $record->user?->name . ' (Dpto. ' . ($record->user?->departamento?->numero ?? 'N/A') . ')')
                            ->color('gray')
                            ->size('xs'),

                        Tables\Columns\TextColumn::make('descripcion')
                            ->limit(70)
                            ->color('gray')
                            ->size('xs'),
                    ])->space(2),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Anuncio')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->button(),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn (Anuncio $record) => $record->user_id === auth()->id()),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (Anuncio $record) => $record->user_id === auth()->id()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AnuncioResource\Pages\ListAnuncios::route('/'),
        ];
    }
}