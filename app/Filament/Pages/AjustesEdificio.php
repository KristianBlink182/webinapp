<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Condominio;

class AjustesEdificio extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Ajustes del Edificio';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $title = 'Identidad y Cámara de Seguridad';
    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.ajustes-edificio';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $condo = $tenant ?? Condominio::find(auth()->user()->condominio_id ?? 1);

        if ($condo) {
            $this->form->fill($condo->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Logo de la Residencial / Condominio')
                    ->description('Suba la insignia del edificio para Modo Oscuro y Modo Claro. Aparecerán en la cabecera y recibos en PDF.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Modo Oscuro (Texto Blanco / Claro)')
                            ->disk('public')
                            ->directory('logos-condominios')
                            ->visibility('public')
                            ->previewable(false)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                            ->maxSize(5120),

                        Forms\Components\FileUpload::make('logo_claro')
                            ->label('Logo Modo Claro (Texto Oscuro / Negro)')
                            ->disk('public')
                            ->directory('logos-condominios')
                            ->visibility('public')
                            ->previewable(false)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                            ->maxSize(5120),
                    ])->columns(2),

                Forms\Components\Section::make('Cámara de Seguridad de la Puerta Principal')
                    ->description('Ingrese la URL de transmisión en vivo o enlace de la cámara para que los vecinos la vean desde su App.')
                    ->schema([
                        Forms\Components\TextInput::make('url_camara_principal')
                            ->label('Enlace / URL de la Cámara en Vivo')
                            ->placeholder('Ej: https://www.youtube.com/watch?v=... o enlace de la cámara')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Convertir arrays de subida FileUpload a texto simple (string) para MySQL
        if (isset($data['logo']) && is_array($data['logo'])) {
            $data['logo'] = reset($data['logo']) ?: null;
        }
        if (isset($data['logo_claro']) && is_array($data['logo_claro'])) {
            $data['logo_claro'] = reset($data['logo_claro']) ?: null;
        }

        $tenant = \Filament\Facades\Filament::getTenant();
        $condo = $tenant ?? Condominio::find(auth()->user()->condominio_id ?? 1);

        if ($condo) {
            $condo->update($data);

            Notification::make()
                ->title('Ajustes Guardados')
                ->body('El logo y la cámara de seguridad han sido actualizados con éxito.')
                ->success()
                ->send();
        }
    }
}