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
        $table->id(); // Tu INT AUTO_INCREMENT PRIMARY KEY
        $table->string('nombre', 100)->nullable();
        $table->string('apellido_paterno', 100)->nullable();
        $table->string('apellido_materno', 100)->nullable();
        $table->string('tipo_documento', 20)->nullable();
        $table->string('numero_documento', 20)->unique(); // UNIQUE para evitar duplicados críticos [9]
        $table->string('telefono', 20)->nullable();
        $table->text('direccion')->nullable();
        $table->string('localidad', 100)->nullable();
        $table->timestamps(); // Crea created_at y updated_at automáticamente
        $table->softDeletes(); // Crea deleted_at para no borrar datos financieros sensible [6]
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
