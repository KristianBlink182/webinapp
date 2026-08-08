<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContactoEmergencia extends Model {
    protected $fillable = ['nombre', 'telefono', 'categoria'];
}