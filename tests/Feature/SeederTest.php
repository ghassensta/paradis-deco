<?php

namespace Tests\Feature;

use Database\Seeders\ProfilSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    /** TEST 10 — le seeder doit pouvoir être rejoué sans doublon ni erreur. */
    public function test_le_seeder_est_idempotent(): void
    {
        $counts = fn () => [
            'products' => \App\Models\Product::count(),
            'categories' => \App\Models\Category::count(),
            'clients' => \App\Models\Client::count(),
            'statuts' => \DB::table('statuts')->count(),
            'configurations' => \App\Models\Configuration::count(),
            'users' => \App\Models\User::count(),
        ];

        // Le seeder s'exécute sans protection d'assignation de masse,
        // comme le fait la commande db:seed.
        Model::unguarded(function () {
            $this->seed(ProfilSeeder::class);
        });

        $apres1 = $counts();

        Model::unguarded(function () {
            $this->seed(ProfilSeeder::class);
            $this->seed(ProfilSeeder::class);
        });

        $this->assertSame($apres1, $counts(), 'Rejouer le seeder ne doit rien dupliquer.');
        $this->assertGreaterThan(0, $apres1['categories']);
    }

    /** Chaque produit du seeder est rattaché à une catégorie. */
    public function test_le_seeder_rattache_tous_les_produits(): void
    {
        Model::unguarded(fn () => $this->seed(ProfilSeeder::class));

        $orphelins = \App\Models\Product::get()->filter(fn ($p) => empty($p->category_ids));

        $this->assertCount(0, $orphelins, 'Tous les produits du seeder doivent avoir une catégorie.');
    }
}
