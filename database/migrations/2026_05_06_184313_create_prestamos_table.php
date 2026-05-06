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
    Schema::create('prestamos', function (Blueprint $table) {
        $table->id();
        // Relación con el cliente (usando su PK personalizada 'persona_id')
        $table->foreignId('cliente_id')->constrained('clientes', 'persona_id');
        // Relación con el Aval (que es una Persona)
        $table->foreignId('aval_id')->constrained('personas');
        // Relación con el Grupo
        $table->foreignId('grupo_id')->constrained('grupos');
        
        // Montos con alta precisión (19 dígitos, 4 decimales)
        $table->decimal('monto_total', 19, 4);
        $table->decimal('monto_entregado', 19, 4);
        $table->decimal('tasa_interes', 5, 2);
        
        $table->date('fecha_inicio');
        $table->integer('semanas'); // Duración del préstamo
        $table->string('estado', 30)->default('pendiente'); // pendiente, activo, pagado, etc.
        $table->boolean('es_extendido')->default(false);
        $table->date('fecha_recuperado')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
