<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personals', function (Blueprint $table) {
            if (!Schema::hasColumn('personals', 'puesto')) {
                $table->string('puesto')->nullable();
            }
            if (!Schema::hasColumn('personals', 'cargo')) {
                $table->string('cargo')->nullable();
            }
            if (!Schema::hasColumn('personals', 'turno')) {
                $table->string('turno')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};