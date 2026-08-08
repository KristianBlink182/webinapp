<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->must_change_password && !Str::contains($request->url(), ['profile', 'logout'])) {
            Notification::make()
                ->title('🔑 Cambio de Contraseña Requerido')
                ->body('Por seguridad, al ser su primer ingreso con clave temporal, debe actualizar su contraseña personal.')
                ->warning()
                ->persistent()
                ->send();

            return redirect()->to(url()->current() . '/profile');
        }

        return $next($request);
    }
}