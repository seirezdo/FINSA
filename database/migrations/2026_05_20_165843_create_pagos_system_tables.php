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
    // Tabla para proyectar las 12 semanas (o más si hay mora)
    Schema::create('calendario_pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('prestamo_id')->constrained()->onDelete('cascade');
        $table->integer('numero_semana'); // 1, 2, 3...
        $table->date('fecha_vencimiento');
        $table->decimal('monto_esperado', 10, 2); // El 12.5% del préstamo
        $table->enum('estado', ['pendiente', 'parcial', 'pagado', 'vencido'])->default('pendiente');
        $table->timestamps();
    });

    // Tabla para registrar el dinero real que entra
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('calendario_pago_id')->constrained('calendario_pagos')->onDelete('cascade');
        $table->decimal('monto_pagado', 10, 2);
        $table->date('fecha_pago');
        $table->string('metodo_pago')->default('efectivo');
        $table->foreignId('registrado_por')->constrained('users'); // Quién cobró
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_system_tables');
    }
};
