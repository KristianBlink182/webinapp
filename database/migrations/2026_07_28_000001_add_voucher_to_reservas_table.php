<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (!Schema::hasColumn('reservas', 'departamento_id')) {
                $table->foreignId('departamento_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('reservas', 'voucher')) {
                $table->string('voucher')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'departamento_id')) {
                $table->dropColumn('departamento_id');
            }
            if (Schema::hasColumn('reservas', 'voucher')) {
                $table->dropColumn('voucher');
            }
        });
    }
};