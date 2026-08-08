<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            if (!Schema::hasColumn('gastos', 'concepto')) {
                $table->string('concepto')->nullable()->after('condominio_id');
            }
            if (!Schema::hasColumn('gastos', 'concepto_detalle')) {
                $table->text('concepto_detalle')->nullable()->after('concepto');
            }
            if (!Schema::hasColumn('gastos', 'mes')) {
                $table->string('mes')->nullable()->after('monto');
            }
            if (!Schema::hasColumn('gastos', 'anio')) {
                $table->integer('anio')->nullable()->after('mes');
            }
            if (!Schema::hasColumn('gastos', 'numero_factura')) {
                $table->string('numero_factura')->nullable()->after('anio');
            }
            if (!Schema::hasColumn('gastos', 'fecha_factura')) {
                $table->date('fecha_factura')->nullable()->after('numero_factura');
            }
            if (!Schema::hasColumn('gastos', 'comprobante')) {
                $table->string('comprobante')->nullable()->after('fecha_factura');
            }
            if (!Schema::hasColumn('gastos', 'proveedor_id')) {
                $table->foreignId('proveedor_id')->nullable()->after('comprobante');
            }
        });
    }

    public function down(): void {}
};