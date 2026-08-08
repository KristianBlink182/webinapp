<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('visitas', function (Blueprint $table) {
            // Estado: Programada (por el vecino), Ingresada (en el edificio), Finalizada (ya salió)
            $table->string('estado_visita')->default('Ingresada'); 
        });
    }
    public function down(): void {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn('estado_visita');
        });
    }
};