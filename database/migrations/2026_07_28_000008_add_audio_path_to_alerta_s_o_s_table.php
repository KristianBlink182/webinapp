<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('alerta_s_o_s', 'audio_path')) {
            Schema::table('alerta_s_o_s', function (Blueprint $table) {
                $table->string('audio_path')->nullable()->after('descripcion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('alerta_s_o_s', 'audio_path')) {
            Schema::table('alerta_s_o_s', function (Blueprint $table) {
                $table->dropColumn('audio_path');
            });
        }
    }
};