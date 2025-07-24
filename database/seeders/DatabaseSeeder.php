<?php

namespace Database\Seeders;

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
        $this->call([
            SuperAdminSeeder::class, // Adicione esta linha para chamar seu SuperAdminSeeder
            // Outros seeders que você possa ter, como InstitutionSeeder, etc.
        ]);
    }
}
