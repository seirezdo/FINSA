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
    $table->foreignId('plaza_id')->constrained('plazas');
$table->foreignId('promotora_id')->nullable()->constrained('users');
    $table->string('nombre', 100);
 $table->string('dia_reunion');
    $table->string('estado', 20)->default('FORMACION');
    $table->date('fecha_creacion')->useCurrent(); 
    $table->timestamps();
    $table->softDeletes();
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
