<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personals', function (Blueprint $table) {
            if (!Schema::hasColumn('personals', 'nombre')) {
                $table->string('nombre')->nullable();
            }
            if (!Schema::hasColumn('personals', 'dni')) {
                $table->string('dni')->nullable();
            }
            if (!Schema::hasColumn('personals', 'cargo')) {
                $table->string('cargo')->default('Vigilante');
            }
            if (!Schema::hasColumn('personals', 'telefono')) {
                $table->string('telefono')->nullable();
            }
            if (!Schema::hasColumn('personals', 'turno')) {
                $table->string('turno')->default('Mañana');
            }
            if (!Schema::hasColumn('personals', 'estado')) {
                $table->string('estado')->default('Activo');
            }
        });
    }

    public function down(): void
    {
        //
    }
};