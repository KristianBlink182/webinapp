<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->foreignId('departamento_id')->constrained()->onDelete('cascade');
            $table->string('nombre_visitante');
            $table->string('dni_visitante')->nullable();
            $table->string('motivo')->nullable(); // Ej: Delivery, Familiar, Técnico
            $table->dateTime('fecha_entrada')->default(now());
            $table->dateTime('fecha_salida')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('visitas'); }
};