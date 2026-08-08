<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('votacions', 'documento_adjunto')) {
            Schema::table('votacions', function (Blueprint $table) {
                $table->string('documento_adjunto')->nullable()->after('descripcion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('votacions', 'documento_adjunto')) {
            Schema::table('votacions', function (Blueprint $table) {
                $table->dropColumn('documento_adjunto');
            });
        }
    }
};