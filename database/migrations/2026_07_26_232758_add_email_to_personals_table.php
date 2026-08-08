<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('personals')) {
            Schema::table('personals', function (Blueprint $table) {
                if (!Schema::hasColumn('personals', 'email')) {
                    $table->string('email')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};