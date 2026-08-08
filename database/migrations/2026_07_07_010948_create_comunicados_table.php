<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('contenido');
            $table->enum('tipo', ['Información', 'Urgente', 'Mantenimiento'])->default('Información');
            $table->date('fecha_publicacion')->default(now());
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('comunicados'); }
};