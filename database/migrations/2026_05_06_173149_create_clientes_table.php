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
        // Vinculamos con la persona. persona_id será la llave primaria y foránea.
        $table->foreignId('persona_id')->primary()->constrained('personas')->onDelete('cascade');
        $table->date('fecha_registro');
        $table->string('estado', 20)->default('activo');
        $table->boolean('kyc_completado')->default(false);
        $table->string('geolocalizacion_domicilio', 100)->nullable();
        $table->string('perfil_riesgo', 20)->nullable();
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
