<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alerta_s_o_s')) {
            Schema::table('alerta_s_o_s', function (Blueprint $table) {
                if (!Schema::hasColumn('alerta_s_o_s', 'tipo')) {
                    $table->string('tipo')->default('Medica')->after('id');
                }
                if (!Schema::hasColumn('alerta_s_o_s', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
                if (!Schema::hasColumn('alerta_s_o_s', 'condominio_id')) {
                    $table->foreignId('condominio_id')->nullable();
                }
                if (!Schema::hasColumn('alerta_s_o_s', 'departamento_id')) {
                    $table->foreignId('departamento_id')->nullable();
                }
                if (!Schema::hasColumn('alerta_s_o_s', 'user_id')) {
                    $table->foreignId('user_id')->nullable();
                }
                if (!Schema::hasColumn('alerta_s_o_s', 'estado')) {
                    $table->string('estado')->default('Pendiente');
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};