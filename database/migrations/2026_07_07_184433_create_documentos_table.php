<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            // Esta es la columna que el error dice que falta:
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->string('archivo');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('documentos');
    }
};