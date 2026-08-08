<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;

class SeguridadHub extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Seguridad Hub';
    protected static ?string $title = 'Seguridad del Condominio';
    protected static ?string $navigationGroup = 'Seguridad';

    protected static string $view = 'filament.vecino.pages.seguridad-hub';
}