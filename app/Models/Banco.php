<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $guarded = [];

    public function condominio()
    {
        return $this->belongsTo(Condominio::class);
    }
}