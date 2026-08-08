<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anuncios', function (Blueprint $table) {
            $table->id();
            // Relación con el vecino que vende
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Relación con el condominio para que solo lo vean los vecinos del mismo edificio
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            
            $table->string('producto');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('foto')->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('esta_vendido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anuncios');
    }
};