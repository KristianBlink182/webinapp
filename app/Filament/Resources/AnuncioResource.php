<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnuncioResource\Pages;
use App\Models\Anuncio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class AnuncioResource extends Resource
{
    protected static ?string $model = Anuncio::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    // CAMBIO DE NOMBRE A MARKETPLACE
    protected static ?string $navigationLabel = 'Marketplace';
    protected static ?string $pluralModelLabel = 'Marketplace';
    protected static ?string $modelLabel = 'Anuncio';
    
    protected static ?string $navigationGroup = 'Comunidad';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Publicar en Marketplace')
                ->schema([
                    Forms\Components\TextInput::make('producto')->label('¿Qué vendes?')->required(),
                    Forms\Components\TextInput::make('precio')->label('Precio S/')->numeric()->prefix('S/')->required(),
                    Forms\Components\TextInput::make('whatsapp')->label('WhatsApp')->required(),
                    Forms\Components\FileUpload::make('foto')->image()->directory('market')->disk('public')->required(),
                    Forms\Components\Textarea::make('descripcion')->label('Descripción')->required()->columnSpanFull(),
                ])->columns(2)
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoSection::make('Detalles del Producto')
                ->headerActions([
                    \Filament\Infolists\Components\Actions\Action::make('comprar')
                        ->label('Contactar por WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->url(fn ($record) => "https://wa.me/51" . $record->whatsapp)
                        ->openUrlInNewTab(),
                ])
                ->schema([
                    ImageEntry::make('foto')->label('')->disk('public')->columnSpanFull()->alignCenter(),
                    TextEntry::make('producto')->weight('bold')->size('lg'),
                    TextEntry::make('precio')->money('PEN')->color('success'),
                    TextEntry::make('user.name')->label('Vendedor'),
                    TextEntry::make('descripcion')->columnSpanFull(),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')->disk('public')->rounded(),
                Tables\Columns\TextColumn::make('producto')->searchable(),
                Tables\Columns\TextColumn::make('precio')->money('PEN'),
                Tables\Columns\TextColumn::make('esta_vendido')->label('Estado')->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Vendido' : 'Disponible')
                    ->color(fn ($state) => $state ? 'gray' : 'success'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnuncios::route('/'),
            'create' => Pages\CreateAnuncio::route('/create'),
            'view' => Pages\ViewAnuncio::route('/{record}'),
            'edit' => Pages\EditAnuncio::route('/{record}/edit'),
        ];
    }
}