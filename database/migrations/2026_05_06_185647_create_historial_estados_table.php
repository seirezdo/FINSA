<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('historial_estados', function (Blueprint $table) {
        $table->id();
        // Crea entidad_id y entidad_type automáticamente
        $table->morphs('entidad'); 
        
        $table->string('estado_anterior', 30)->nullable();
        $table->string('estado_nuevo', 30);
        
        // El usuario que realizó el cambio (vinculado a la tabla users de Breeze)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->text('motivo')->nullable();
        $table->timestamps(); // Registra la fecha exacta del cambio
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
    }
};
