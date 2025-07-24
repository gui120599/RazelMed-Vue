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
        Schema::create('dashboard_user', function (Blueprint $table) {
            $table->foreignId('dashboard_id')->constrained('dashboards'); // Garante que a FK aponta para a tabela 'dashboards'
            $table->foreignId('user_id')->constrained('users'); // Garante que a FK aponta para a tabela 'users'
            $table->primary(['dashboard_id', 'user_id']); // Garante que cada par dashboard-usuário é único
            $table->timestamps(); // Opcional, mas útil para auditoria de quando a permissão foi dada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_users');
    }
};
