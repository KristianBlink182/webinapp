<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class NuevoReciboNotification extends Notification
{
    use Queueable;

    public $pago;

    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Generamos el PDF para adjuntarlo
        $pdf = Pdf::loadView('recibo', ['pago' => $this->pago]);

        return (new MailMessage)
            ->subject('LIVO: Tu recibo de ' . $this->pago->mes . ' está listo')
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Se ha generado el recibo de mantenimiento de tu unidad.')
            ->line('Monto total: S/ ' . number_format($this->pago->monto, 2))
            ->attachData($pdf->output(), 'recibo-livo.pdf', [
                'mime' => 'application/pdf',
            ])
            ->action('Ver en el sistema', url('/admin'))
            ->line('Gracias por usar LIVO.');
    }
}