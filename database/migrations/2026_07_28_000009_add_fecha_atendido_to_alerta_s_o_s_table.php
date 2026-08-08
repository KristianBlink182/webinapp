<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('alerta_s_o_s', 'fecha_atendido')) {
            Schema::table('alerta_s_o_s', function (Blueprint $table) {
                $table->dateTime('fecha_atendido')->nullable()->after('estado');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('alerta_s_o_s', 'fecha_atendido')) {
            Schema::table('alerta_s_o_s', function (Blueprint $table) {
                $table->dropColumn('fecha_atendido');
            });
        }
    }
};