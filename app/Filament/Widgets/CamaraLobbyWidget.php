<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CamaraLobbyWidget extends Widget
{
    protected static string $view = 'filament.widgets.camara-lobby-widget';

    protected static ?int $sort = 20;

   protected int | string | array $columnSpan = 1;
}