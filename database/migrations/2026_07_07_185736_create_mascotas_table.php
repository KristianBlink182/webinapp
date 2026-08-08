<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->enum('especie', ['Perro', 'Gato', 'Otro'])->default('Perro');
            $table->string('raza')->nullable();
            $table->string('foto')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mascotas'); }
};