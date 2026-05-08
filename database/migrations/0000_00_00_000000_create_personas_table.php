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
    Schema::create('personas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100)->nullable();
        $table->string('apellido_paterno', 100)->nullable();
        $table->string('apellido_materno', 100)->nullable();
        $table->string('tipo_documento', 20)->nullable();
        
        // CORRECCIÓN: Ahora permite ser nulo para registro rápido
        $table->string('numero_documento', 20)->nullable()->unique(); 
        
        $table->string('telefono', 20)->nullable();
        $table->text('direccion')->nullable();
        $table->string('localidad', 100)->nullable();
        $table->timestamps();
        $table->softDeletes(); // Protección para datos financieros [5]
    });
}

public function down(): void
{
    Schema::dropIfExists('personas');
}
};
