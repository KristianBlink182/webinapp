<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('nombre_completo');
            $table->string('dni')->nullable();
            $table->string('puesto'); // Ej: Portero, Limpieza, Mantenimiento
            $table->string('telefono')->nullable();
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche', 'Rotativo'])->default('Mañana');
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('personals'); }
};