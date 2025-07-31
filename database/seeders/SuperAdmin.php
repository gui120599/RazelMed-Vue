<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o Super Admin já existe para evitar duplicatas em execuções repetidas
        if (!User::where('email', 'guilherme.santos@razeltec.com.br')->exists()) {
            User::create([
                'name' => 'Guilherme Marins dos Santos',
                'email' => 'guilherme.santos@razeltec.com.br', // Email padrão para o Super Admin
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
