<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('area_comuns', function (Blueprint $table) {
            $table->integer('total_mesas')->default(1); // Para saber cuántas mesas tiene
            $table->integer('capacidad_maxima')->default(10); // Capacidad de personas
        });
        Schema::table('reservas', function (Blueprint $table) {
            $table->integer('cantidad_personas')->nullable();
            $table->integer('numero_mesa')->nullable(); // La mesa que eligió
        });
    }
    public function down(): void {
        Schema::table('area_comuns', function ($table) { $table->dropColumn(['total_mesas', 'capacidad_maxima']); });
        Schema::table('reservas', function ($table) { $table->dropColumn(['cantidad_personas', 'numero_mesa']); });
    }
};