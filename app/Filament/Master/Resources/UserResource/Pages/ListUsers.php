<?php

namespace App\Filament\Master\Resources\UserResource\Pages;

use App\Filament\Master\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Gestión de Usuarios del SaaS';

    /**
     * 🎯 BOTÓN DESTACADO PARA CREAR NUEVO ADMINISTRADOR O USUARIO
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Usuario / Admin')
                ->icon('heroicon-m-user-plus'),
        ];
    }

    /**
     * 🎯 PESTAÑAS DE NAVEGACIÓN
     */
    public function getTabs(): array
    {
        return [
            'superadmins' => Tab::make('Equipo SaaS (Master)')
                ->icon('heroicon-m-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'superadmin'))
                ->badge(User::where('role', 'superadmin')->count()),

            'admins' => Tab::make('Administradores de Edificios')
                ->icon('heroicon-m-user-group')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
                ->badge(User::where('role', 'admin')->count()),

            'residentes' => Tab::make('Residentes / Vecinos')
                ->icon('heroicon-m-home')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'residente'))
                ->badge(User::where('role', 'residente')->count()),

            'vigilantes' => Tab::make('Portería')
                ->icon('heroicon-m-eye')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'vigilante'))
                ->badge(User::where('role', 'vigilante')->count()),

            'todos' => Tab::make('Todos')
                ->badge(User::count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'admins';
    }
}