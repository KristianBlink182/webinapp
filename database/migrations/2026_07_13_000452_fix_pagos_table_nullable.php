<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('pagos', function (Blueprint $table) {
        $table->decimal('lectura_anterior', 10, 2)->nullable()->change();
        $table->decimal('lectura_actual', 10, 2)->nullable()->change();
        $table->decimal('monto_mantenimiento', 10, 2)->default(0)->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
