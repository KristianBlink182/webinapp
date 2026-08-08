<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (!Schema::hasColumn('condominios', 'comprobante_factura_saas')) {
                $table->string('comprobante_factura_saas')->nullable()->after('voucher_saas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (Schema::hasColumn('condominios', 'comprobante_factura_saas')) {
                $table->dropColumn('comprobante_factura_saas');
            }
        });
    }
};