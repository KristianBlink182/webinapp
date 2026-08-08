<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contacto_emergencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); 
            $table->string('telefono');
            $table->string('categoria'); 
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('contacto_emergencias'); }
};