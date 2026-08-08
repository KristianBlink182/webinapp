<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('departamentos', 'estacionamiento')) {
                $table->string('estacionamiento')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'nombre_propietario')) {
                $table->string('nombre_propietario')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'telefono_propietario')) {
                $table->string('telefono_propietario')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'email_propietario')) {
                $table->string('email_propietario')->nullable();
            }
            if (!Schema::hasColumn('departamentos', 'condicion')) {
                $table->string('condicion')->default('Propietario');
            }
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
        //
    }
};