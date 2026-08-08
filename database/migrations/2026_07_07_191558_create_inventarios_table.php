<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // Ej: Cortadora de césped, Taladro, Pintura Blanca
            $table->string('descripcion')->nullable();
            $table->integer('cantidad')->default(1);
            $table->string('unidad_medida')->default('unidades'); // Unidades, Litros, Galones
            $table->enum('estado', ['Nuevo', 'Bueno', 'Regular', 'Mal Estado'])->default('Bueno');
            $table->string('ubicacion')->nullable(); // Ej: Cuarto de máquinas, Almacén 1
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inventarios'); }
};