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
$table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
// Cambiamos 'personas' por 'clientes' y lo hacemos opcional (nullable) por si algún préstamo se da sin aval
$table->foreignId('aval_id')->nullable()->constrained('clientes');
$table->foreignId('grupo_id')->constrained('grupos');
            
        // Ajustamos los nombres para que coincidan con el controlador
        $table->decimal('monto_total_pagar', 19, 4); // Antes: monto_total
        $table->decimal('monto_prestado', 19, 4);     // Antes: monto_entregado
        $table->decimal('tasa_interes', 5, 2);
        
        $table->date('fecha_inicio');
        $table->integer('semanas'); 
        $table->string('estado', 30)->default('activo'); // Cambiado a 'activo' por defecto según el Plan Maestro
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
