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
        // Conexión obligatoria con tu tabla raíz
        $table->foreignId('persona_id')->nullable()->constrained('personas')->onDelete('set null');
        
        $table->string('name'); 
        $table->string('email')->unique(); // Columna crítica para Breeze
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('estado', 20)->default('activo'); // Tu campo personalizado
        $table->rememberToken();
        $table->timestamps();
    });

    // Estas tablas son necesarias para el funcionamiento de Breeze
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
