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
    Schema::table('condominios', function (Blueprint $table) {
        $table->string('metodo_pago_preferido')->default('Transferencia'); // Yape, Tarjeta, Recaudadora
        $table->string('yape_numero')->nullable();
        $table->string('yape_qr')->nullable(); // Imagen del QR
        $table->text('instrucciones_banco')->nullable(); // Para Cuentas Recaudadoras o BCP
        $table->string('pasarela_llave')->nullable(); // Para Culqi/Niubiz (Tarjetas)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            //
        });
        
    }
    
};
