<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Notifications\Notification;

class Anuncio extends Model
{
    use HasFactory;

    protected $fillable = [
        'condominio_id',
        'user_id',
        'producto',
        'titulo',
        'descripcion',
        'precio',
        'imagen',
        'telefono_whatsapp',
        'estado',
    ];

    // 🔔 DISPARO DE NOTIFICACIONES POP-UP A TODOS LOS USUARIOS
    protected static function booted(): void
    {
        static::created(function (Anuncio $anuncio) {
            $vendedor = auth()->user();

            // 1. Pop-Up verde flotante inmediato en pantalla
            Notification::make()
                ->title('🛍️ ¡Anuncio Publicado!')
                ->body("Tu producto '{$anuncio->producto}' ya fue registrado en el Marketplace.")
                ->icon('heroicon-o-check-circle')
                ->success()
                ->send();

            // 2. Guarda la notificación en la campanita de TODOS los usuarios registrados
            $todosLosUsuarios = User::all();

            foreach ($todosLosUsuarios as $usuario) {
                Notification::make()
                    ->title('🛍️ ¡Nuevo anuncio en el Marketplace!')
                    ->body(($vendedor?->name ?? 'Un vecino') . " publicó: '{$anuncio->producto}' por S/ " . number_format($anuncio->precio, 2))
                    ->icon('heroicon-o-shopping-bag')
                    ->success()
                    ->sendToDatabase($usuario);
            }
        });
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}