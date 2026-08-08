<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\EgresosChart;
use App\Filament\Widgets\CamaraLobbyWidget;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return '';
    }

    protected int | string | array $columns = 2;

    public function getWidgets(): array
    {
        return [
            EgresosChart::class,
            CamaraLobbyWidget::class,
        ];
    }
}