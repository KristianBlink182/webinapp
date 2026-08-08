<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained()->onDelete('cascade');
            $table->string('destinatario'); // Nombre de la persona que recibe
            $table->string('empresa_envio')->nullable(); // Ej: Amazon, Olva, DHL
            $table->text('descripcion')->nullable(); // Ej: Caja mediana, Sobre, etc.
            $table->string('foto')->nullable(); // 📸 FOTO DEL PAQUETE
            $table->dateTime('fecha_recibido')->default(now());
            $table->dateTime('fecha_entregado')->nullable();
            $table->enum('estado', ['En Recepción', 'Entregado'])->default('En Recepción');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('paquetes'); }
};