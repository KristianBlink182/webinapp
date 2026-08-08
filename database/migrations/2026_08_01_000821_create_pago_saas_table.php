<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_saas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained('condominios')->onDelete('cascade');
            $table->string('plan')->nullable();
            $table->decimal('monto', 10, 2)->default(0);
            $table->string('voucher')->nullable();
            $table->string('estado')->default('Pago por Verificar');
            $table->string('comprobante_factura')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_saas');
    }
};