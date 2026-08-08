<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pagos', function (Blueprint $table) {
            $table->decimal('monto_mora', 10, 2)->default(0); // Columna para la multa
        });
    }
    public function down(): void {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('monto_mora');
        });
    }
};