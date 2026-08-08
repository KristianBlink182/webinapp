<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;

class FinanzasHub extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Finanzas Hub';
    protected static ?string $title = 'Mis Finanzas & Recibos';
    protected static ?string $navigationGroup = 'Finanzas';

    protected static string $view = 'filament.vecino.pages.finanzas-hub';
}