<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            if (!Schema::hasColumn('condominios', 'logo')) {
                $table->string('logo')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};