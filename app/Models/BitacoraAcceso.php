<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraAcceso extends Model
{
    use HasFactory;

    protected $table = 'bitacora_accesos';

    protected $fillable = [
        'user_id',
        'user_name',
        'condominio_nombre',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}