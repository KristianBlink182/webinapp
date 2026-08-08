<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('voucher')->nullable(); // Para guardar la foto del pago
            $table->text('nota_administrador')->nullable(); // Por si rechazan el pago
        });
    }
    public function down(): void {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['voucher', 'nota_administrador']);
        });
    }
};