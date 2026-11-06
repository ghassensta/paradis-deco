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
        $this->call(ProfilSeeder::class);

        // Données de démonstration : les avis s'appuient sur les produits
        // existants, ils passent donc toujours en dernier.
        $this->call([
            InspirationSeeder::class,
            AvisSeeder::class,
        ]);
    }
}
