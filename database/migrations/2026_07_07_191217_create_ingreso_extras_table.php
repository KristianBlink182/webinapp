<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // El nombre debe ser exactamente 'ingreso_extras'
        Schema::create('ingreso_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->foreignId('departamento_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->enum('categoria', ['Multa', 'Alquiler', 'Donación', 'Otro'])->default('Multa');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['Pendiente', 'Pagado'])->default('Pendiente');
            $table->date('fecha_registro')->default(now());
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ingreso_extras');
    }
};