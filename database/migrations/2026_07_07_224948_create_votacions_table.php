<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Creamos la tabla 'votacions'
        Schema::create('votacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->json('opciones'); 
            $table->date('fecha_limite');
            $table->boolean('esta_activa')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('votacions');
    }
};