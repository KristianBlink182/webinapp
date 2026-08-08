<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anuncios')) {
            Schema::table('anuncios', function (Blueprint $table) {
                if (!Schema::hasColumn('anuncios', 'telefono_whatsapp')) {
                    $table->string('telefono_whatsapp')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};