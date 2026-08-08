<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model {
    protected $fillable = ['condominio_id', 'titulo', 'archivo'];

    public function condominio(): BelongsTo {
        return $this->belongsTo(Condominio::class);
    }
}