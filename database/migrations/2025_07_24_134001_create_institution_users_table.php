<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institution_user', function (Blueprint $table) {
            $table->foreignId('institution_id')->constrained('institutions');
            $table->foreignId('user_id')->constrained('users');
            $table->primary(['institution_id', 'user_id']); // Garante que cada par instituição-usuário é único
            $table->timestamps(); // Opcional, mas útil para auditoria
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_users');
    }
};
