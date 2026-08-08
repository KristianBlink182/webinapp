<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            // Agregamos las columnas que faltan para los métodos de pago
            if (!Schema::hasColumn('condominios', 'acepta_yape')) {
                $table->boolean('acepta_yape')->default(false);
            }
            if (!Schema::hasColumn('condominios', 'acepta_transferencia')) {
                $table->boolean('acepta_transferencia')->default(true);
            }
            if (!Schema::hasColumn('condominios', 'acepta_tarjeta')) {
                $table->boolean('acepta_tarjeta')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropColumn(['acepta_yape', 'acepta_transferencia', 'acepta_tarjeta']);
        });
    }
};