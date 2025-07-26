<?php

namespace Database\Seeders;

use App\Models\Dashboard;
use App\Models\Institution;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Gerar 20 clientes
        $institution = Institution::factory()->count(5)->create(); // Gera 5 clientes com dados fictícios

        User::factory()->count(5)->create(); // Gera 5 clientes com dados fictícios

        // generate 5 users
        Dashboard::factory()
            ->count(20)
            ->state(function () use ($institution) {
                return ['institution_id' => $institution->random()->id];
            })
            ->create();

    }
}
