<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('departamentos', function (Blueprint $table) {
        $table->string('dni_propietario')->nullable(); // DNI que sale en el recibo
    });

    Schema::table('pagos', function (Blueprint $table) {
        $table->decimal('lectura_anterior', 8, 2)->default(0);
        $table->decimal('lectura_actual', 8, 2)->default(0);
        $table->string('foto_medidor')->nullable(); // Foto de transparencia
        $table->decimal('monto_mantenimiento', 10, 2)->default(0);
        $table->decimal('monto_agua', 10, 2)->default(0);
        $table->decimal('monto_luz', 10, 2)->default(0);
        $table->decimal('saldo_anterior', 10, 2)->default(0);
    });
}
};
