<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Define la estructura física de la tabla en MySQL.
     */
    public function up(): void
    {
        Schema::create('plazas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
          $table->string('zona', 100)->nullable();
            
            // Relaciones con la tabla personas para ejecutivo y supervisora
          $table->foreignId('ejecutivo_id')->constrained('users')->onDelete('cascade');
         $table->foreignId('supervisora_id')->constrained('users')->onDelete('cascade');
            
            $table->string('estado', 20)->default('activo');
            $table->timestamps();
            $table->softDeletes(); // IMPORTANTE: Agregamos esto para trazabilidad financiera [3]
        });
    }

    /**
     * Revierte los cambios si es necesario.
     */
    public function down(): void
    {
        Schema::dropIfExists('plazas');
    }
};