<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // El vecino que reporta
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('foto')->nullable(); // Para que suban fotos del problema
            $table->enum('prioridad', ['Baja', 'Media', 'Alta'])->default('Media');
            $table->enum('estado', ['Pendiente', 'En Proceso', 'Resuelto'])->default('Pendiente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('reclamos'); }
};