<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;

class CamaraEnVivo extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Cámara de Seguridad';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $title = 'Cámara de Seguridad en Vivo';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.vecino.pages.camara-en-vivo';

    public function getViewData(): array
    {
        $user = auth()->user();
        $condo = $user?->departamento?->condominio;
        $rawUrl = $condo?->url_camara_principal;

        $urlCamara = $rawUrl;

        // CONVERTIDOR AUTOMÁTICO DE YOUTUBE (Transforma watch?v= en /embed/)
        if (!empty($rawUrl)) {
            if (str_contains($rawUrl, 'youtube.com/watch?v=')) {
                $parts = parse_url($rawUrl);
                parse_str($parts['query'] ?? '', $query);
                if (!empty($query['v'])) {
                    $urlCamara = "https://www.youtube.com/embed/" . $query['v'] . "?autoplay=1&mute=1";
                }
            } elseif (str_contains($rawUrl, 'youtu.be/')) {
                $videoId = basename(parse_url($rawUrl, PHP_URL_PATH));
                if (!empty($videoId)) {
                    $urlCamara = "https://www.youtube.com/embed/" . $videoId . "?autoplay=1&mute=1";
                }
            }
        }

        return [
            'urlCamara' => $urlCamara,
            'condominio' => $condo,
        ];
    }
}