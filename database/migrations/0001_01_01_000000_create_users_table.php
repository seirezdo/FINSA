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
         Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique(); // Cambiamos email por username según tu diseño
        $table->string('password');
        // Vinculación con personas
        $table->foreignId('persona_id')->nullable()->constrained('personas')->onDelete('set null');
        $table->string('estado', 20)->default('activo');
        $table->integer('intentos_fallidos')->default(0);
        $table->timestamp('ultimo_acceso')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
