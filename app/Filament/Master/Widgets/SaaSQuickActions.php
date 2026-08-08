<?php

namespace App\Filament\Master\Widgets;

use Filament\Widgets\Widget;

class SaaSQuickActions extends Widget
{
    protected static string $view = 'filament.master.widgets.saas-quick-actions';
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';
}