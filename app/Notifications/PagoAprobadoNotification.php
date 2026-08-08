<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoAprobadoNotification extends Notification
{
    use Queueable;

    public $pago;

    public function __construct(Pago $pago) {
        $this->pago = $pago;
    }

    public function via($notifiable): array {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('¡Pago Confirmado! - Recibo #' . $this->pago->id)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu pago del mes de ' . $this->pago->mes . ' ha sido aprobado por la administración.')
            ->line('Monto recibido: S/ ' . number_format($this->pago->monto, 2))
            ->action('Ver mi Estado de Cuenta', url('/admin/pagos'))
            ->line('Gracias por ser un vecino responsable.');
    }
}