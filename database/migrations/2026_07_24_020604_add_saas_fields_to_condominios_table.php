<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (!Schema::hasColumn('condominios', 'estado_servicio')) {
                $table->string('estado_servicio')->default('Activo');
            }
            if (!Schema::hasColumn('condominios', 'plan_saas')) {
                $table->string('plan_saas')->default('Pro');
            }
            if (!Schema::hasColumn('condominios', 'precio_mensual_saas')) {
                $table->decimal('precio_mensual_saas', 10, 2)->default(150.00);
            }
            if (!Schema::hasColumn('condominios', 'fecha_vencimiento_saas')) {
                $table->date('fecha_vencimiento_saas')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropColumn(['estado_servicio', 'plan_saas', 'precio_mensual_saas', 'fecha_vencimiento_saas']);
        });
    }
};