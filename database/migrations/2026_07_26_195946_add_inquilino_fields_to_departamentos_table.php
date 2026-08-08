<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('departamentos', 'nombre_inquilino')) {
                $table->string('nombre_inquilino')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'telefono_inquilino')) {
                $table->string('telefono_inquilino')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'email_inquilino')) {
                $table->string('email_inquilino')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropColumn(['nombre_inquilino', 'telefono_inquilino', 'email_inquilino']);
        });
    }
};