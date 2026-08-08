<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('area_comuns', function (Blueprint $table) {
            if (!Schema::hasColumn('area_comuns', 'costo')) {
                $table->decimal('costo', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('area_comuns', 'mesas')) {
                $table->integer('mesas')->default(1);
            }
            if (!Schema::hasColumn('area_comuns', 'capacidad_max')) {
                $table->integer('capacidad_max')->default(10);
            }
            if (!Schema::hasColumn('area_comuns', 'reglas')) {
                $table->text('reglas')->nullable();
            }
            if (!Schema::hasColumn('area_comuns', 'estado')) {
                $table->string('estado')->default('Disponible');
            }
        });
    }

    public function down(): void
    {
        //
    }
};