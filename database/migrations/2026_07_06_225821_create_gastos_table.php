<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('descripcion'); // Ej: Limpieza, Luz áreas comunes
            $table->decimal('monto', 10, 2);
            $table->date('fecha_gasto');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('gastos'); }
};