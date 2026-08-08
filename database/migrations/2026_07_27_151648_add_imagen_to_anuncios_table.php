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
                if (!Schema::hasColumn('anuncios', 'imagen')) {
                    $table->string('imagen')->nullable();
                }
                if (!Schema::hasColumn('anuncios', 'producto')) {
                    $table->string('producto')->nullable();
                }
                if (!Schema::hasColumn('anuncios', 'precio')) {
                    $table->decimal('precio', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('anuncios', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};