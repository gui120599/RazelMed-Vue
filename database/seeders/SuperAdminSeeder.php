<?php

namespace Database\Seeders;

use App\Models\User; // Importe o Model User
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Para criptografar a senha

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o Super Admin já existe para evitar duplicatas em execuções repetidas
        if (!User::where('email', 'admin@seuprojeto.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@seuprojeto.com', // Email padrão para o Super Admin
                'password' => Hash::make('password'), // Senha padrão (mude em produção!)
                'is_super_admin' => true,
                'prefer_institution_id' => null, // Super Admin não tem instituição preferencial
                'email_verified_at' => now(), // Define como verificado
            ]);

            $this->command->info('Super Admin user created successfully!');
        } else {
            $this->command->info('Super Admin user already exists.');
        }
    }
}