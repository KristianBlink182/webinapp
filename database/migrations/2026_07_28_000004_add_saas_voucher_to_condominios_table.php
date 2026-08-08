<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (!Schema::hasColumn('condominios', 'voucher_saas')) {
                $table->string('voucher_saas')->nullable()->after('fecha_vencimiento_saas');
            }
            if (!Schema::hasColumn('condominios', 'estado_pago_saas')) {
                $table->string('estado_pago_saas')->nullable()->default('Al Día')->after('voucher_saas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (Schema::hasColumn('condominios', 'voucher_saas')) {
                $table->dropColumn('voucher_saas');
            }
            if (Schema::hasColumn('condominios', 'estado_pago_saas')) {
                $table->dropColumn('estado_pago_saas');
            }
        });
    }
};