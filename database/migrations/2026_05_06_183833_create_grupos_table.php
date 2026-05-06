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
    Schema::create('grupos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100);
        // Un grupo siempre pertenece a una plaza
        $table->foreignId('plaza_id')->constrained('plazas')->onDelete('cascade');
        // El grupo tiene una promotora (que es una persona)
        $table->foreignId('promotora_id')->nullable()->constrained('personas');
        $table->string('estado', 20)->default('activo');
        $table->date('fecha_creacion');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
