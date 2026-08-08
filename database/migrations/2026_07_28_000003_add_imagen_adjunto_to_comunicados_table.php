<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('comunicados', 'imagen_adjunto')) {
            Schema::table('comunicados', function (Blueprint $table) {
                $table->string('imagen_adjunto')->nullable()->after('contenido');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('comunicados', 'imagen_adjunto')) {
            Schema::table('comunicados', function (Blueprint $table) {
                $table->dropColumn('imagen_adjunto');
            });
        }
    }
};