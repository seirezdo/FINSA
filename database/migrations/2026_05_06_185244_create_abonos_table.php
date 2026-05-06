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
    Schema::create('abonos', function (Blueprint $table) {
        $table->id();
        // Relación con el préstamo: si se borra el préstamo, se borran sus abonos
        $table->foreignId('prestamo_id')->constrained('prestamos')->onDelete('cascade');
        
        $table->integer('num_semana'); // Ejemplo: semana 1 de 16
        $table->decimal('monto', 19, 4); // Precisión financiera estricta
        $table->timestamp('fecha_pago')->nullable();
        $table->date('fecha_vencimiento');
        $table->string('estado_pago', 20)->default('pendiente'); // pendiente, pagado, atrasado
        $table->boolean('es_semana_extra')->default(false);
        
        // Llave para evitar cobros duplicados por errores de conexión
        $table->string('idempotency_key', 100)->nullable()->unique();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
    }
};
