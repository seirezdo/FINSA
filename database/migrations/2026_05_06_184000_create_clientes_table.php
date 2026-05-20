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
        // Relación con la identidad física
        $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
        
        // ESTA ES LA COLUMNA QUE TE FALTA: Relación con la jerarquía operativa [3, 4]
        $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
        
        $table->string('curp', 18)->unique(); // Crítico para evitar duplicados [3]
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
