<?php

namespace App\Filament\Vecino\Pages;

use Filament\Pages\Page;
use App\Models\Pago;
use App\Models\Comunicado;
use App\Models\Mascota;
use App\Models\Reclamo;
use App\Models\Paquete;
use App\Models\AlertaSOS;
use App\Models\SismoCheckin;
use Filament\Notifications\Notification;

class Escritorio extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.vecino.pages.dashboard';

    public function getTitle(): string
    {
        $name = auth()->user()?->name ?? 'Residente';
        return "Bienvenido, {$name} 👋";
    }
// 🚨 DISPARO INSTANTÁNEO DE PÁNICO S.O.S (1 TOQUE SIN FORMULARIOS)
    public function dispararSOS(): void
    {
        $user = auth()->user();

        \App\Models\AlertaSOS::create([
            'condominio_id'   => $user->departamento?->condominio_id ?? \Filament\Facades\Filament::getTenant()?->id,
            'departamento_id' => $user->departamento_id,
            'user_id'         => $user->id,
            'tipo'            => 'Emergencia',
            'descripcion'     => '🚨 ALERTA PÁNICO S.O.S DISPARADA EN 1 TOQUE DESDE EL ESCRITORIO',
            'estado'          => 'Pendiente',
        ]);

        \Filament\Notifications\Notification::make()
            ->title('🚨 Alerta S.O.S. Enviada')
            ->body('Se ha notificado de inmediato a la Portería y Administración. ¡Iremos en tu ayuda!')
            ->danger()
            ->send();
    }
    public function responderSismo(string $estado): void
    {
        $user = auth()->user();
        
        if (!$user->departamento_id) {
            Notification::make()->title('Sin Departamento')->warning()->send();
            return;
        }

        SismoCheckin::updateOrCreate(
            [
                'condominio_id' => $user->departamento->condominio_id,
                'user_id' => $user->id,
            ],
            [
                'departamento_id' => $user->departamento_id,
                'estado_seguridad' => $estado,
            ]
        );

        $mensaje = $estado === 'Estoy Bien' 
            ? '🟢 Has confirmado que te encuentras a salvo.' 
            : '🔴 Alerta enviada a Portería y Administración. ¡Iremos a ayudarte!';

        $tipo = $estado === 'Estoy Bien' ? 'success' : 'danger';

        Notification::make()
            ->title('Check-In de Emergencia')
            ->body($mensaje)
            ->$tipo()
            ->send();
    }

    public function getViewData(): array
    {
        $user = auth()->user();
      $condo = \App\Models\Condominio::find($user?->departamento?->condominio_id);
        $sismoActivoEnEdificio = $condo?->sismo_activo ?? false;

        // LÓGICA DE OCULTAMIENTO:
        // El banner solo se muestra si el sismo está activo Y el vecino no ha confirmado estar a salvo ni ha sido atendido por el portero.
        $sismoActivo = false;

        if ($sismoActivoEnEdificio) {
            $checkin = SismoCheckin::where('condominio_id', $condo?->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$checkin) {
                // No ha respondido aún -> Mostrar banner
                $sismoActivo = true;
            } elseif ($checkin->estado_seguridad === 'Necesito Ayuda') {
                // Pidió ayuda y el portero AÚN NO lo ha atendido -> Mantener banner
                $sismoActivo = true;
            } else {
                // Ya dijo "Estoy Bien" o el portero ya lo atendió -> Ocultar banner
                $sismoActivo = false;
            }
        }

        $deudaTotal = Pago::where('departamento_id', $user?->departamento_id)
            ->where('estado', 'Pendiente')
            ->sum('monto') ?? 0;

        $pagoPendiente = Pago::where('departamento_id', $user?->departamento_id)
            ->where('estado', 'Pendiente')
            ->first();

        $ultimoAviso = Comunicado::where('condominio_id', $condo?->id)
            ->latest()
            ->first();

        $paquetesPendientes = Paquete::where('departamento_id', $user?->departamento_id)
            ->where('estado', 'Pendiente')
            ->get();

        $totalMascotas = Mascota::where('departamento_id', $user?->departamento_id)->count();
        $totalReclamos = Reclamo::where('departamento_id', $user?->departamento_id)->count();

        return [
            'user' => $user,
            'sismoActivo' => $sismoActivo,
            'deudaTotal' => $deudaTotal,
            'pagoPendiente' => $pagoPendiente,
            'ultimoAviso' => $ultimoAviso,
            'paquetesPendientes' => $paquetesPendientes,
            'totalMascotas' => $totalMascotas,
            'totalReclamos' => $totalReclamos,
            'condominio' => $condo,
        ];
    }
}