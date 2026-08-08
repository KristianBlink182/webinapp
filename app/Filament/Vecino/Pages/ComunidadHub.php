<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;

class ComunidadHub extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Comunidad Hub';
    protected static ?string $title = 'Comunidad & Servicios del Edificio';
    protected static ?string $navigationGroup = 'Comunidad';

    protected static string $view = 'filament.vecino.pages.comunidad-hub';
}