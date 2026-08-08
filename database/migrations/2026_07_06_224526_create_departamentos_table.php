<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('departamentos', function (Blueprint $table) {
        $table->id();
        // Conectamos con el condominio
        $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
        $table->string('numero'); // Ej: A-402
        $table->integer('piso')->default(1);
        $table->string('nombre_propietario')->nullable();
        $table->string('email_propietario')->nullable();
        $table->decimal('porcentaje_participacion', 5, 2)->default(0); // Para calcular el pago
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
