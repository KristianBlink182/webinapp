<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;

class GestionHub extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Gestión Hub';
    protected static ?string $title = 'Gestión de Espacios del Edificio';
    protected static ?string $navigationGroup = 'Gestión de Espacios';

    protected static string $view = 'filament.vecino.pages.gestion-hub';
}