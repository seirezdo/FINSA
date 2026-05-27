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
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
        
        // ¡Estos campos antes estaban en 'personas', ahora deben estar aquí!
        $table->string('nombre');
        $table->string('curp', 18)->unique();
        $table->string('telefono', 20)->nullable();
        $table->text('direccion')->nullable();
        
        $table->date('fecha_registro')->nullable();
        $table->string('perfil_riesgo')->default('medio');
        $table->string('estado')->default('activo');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
