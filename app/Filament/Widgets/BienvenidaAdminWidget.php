<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class BienvenidaAdminWidget extends Widget
{
    protected static string $view = 'filament.widgets.bienvenida-admin';

    protected static ?int $sort = -200;

    protected int | string | array $columnSpan = 'full';
}