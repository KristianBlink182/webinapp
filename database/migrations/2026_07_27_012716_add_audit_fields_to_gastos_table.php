<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            if (!Schema::hasColumn('gastos', 'concepto_detalle')) {
                $table->string('concepto_detalle')->nullable();
            }
            if (!Schema::hasColumn('gastos', 'numero_factura')) {
                $table->string('numero_factura')->nullable();
            }
            if (!Schema::hasColumn('gastos', 'comprobante')) {
                $table->string('comprobante')->nullable();
            }
            if (!Schema::hasColumn('gastos', 'proveedor_id')) {
                $table->foreignId('proveedor_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};