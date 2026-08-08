<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campos de Facturación Predeterminados en Condominios
        Schema::table('condominios', function (Blueprint $table) {
            if (!Schema::hasColumn('condominios', 'tipo_comprobante_default')) {
                $table->string('tipo_comprobante_default')->default('Boleta');
                $table->string('dni_default')->nullable();
                $table->string('nombre_default')->nullable();
                $table->string('ruc_default')->nullable();
                $table->string('razon_social_default')->nullable();
                $table->text('direccion_fiscal_default')->nullable();
            }
        });

        // Campos de Facturación congelados en cada Pago SaaS
        if (Schema::hasTable('pago_saas')) {
            Schema::table('pago_saas', function (Blueprint $table) {
                if (!Schema::hasColumn('pago_saas', 'tipo_comprobante')) {
                    $table->string('tipo_comprobante')->default('Boleta');
                    $table->decimal('monto_base', 10, 2)->default(0);
                    $table->decimal('monto_igv', 10, 2)->default(0);
                    $table->decimal('monto_total', 10, 2)->default(0);
                    $table->string('dni')->nullable();
                    $table->string('nombre')->nullable();
                    $table->string('ruc')->nullable();
                    $table->string('razon_social')->nullable();
                    $table->text('direccion_fiscal')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Rollback opcional
    }
};