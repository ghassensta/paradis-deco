<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\Inspiration;
use App\Models\User;
use Database\Seeders\InspirationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InspirationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_seeder_cree_exactement_cinq_inspirations_publiees(): void
    {
        $this->seed(InspirationSeeder::class);

        $this->assertSame(5, Inspiration::count());
        $this->assertSame(5, Inspiration::where('is_active', true)->count());
    }

    public function test_le_seeder_est_idempotent(): void
    {
        $this->seed(InspirationSeeder::class);
        $this->seed(InspirationSeeder::class);
        $this->seed(InspirationSeeder::class);

        $this->assertSame(5, Inspiration::count(), 'Rejouer le seeder ne doit pas dupliquer les inspirations.');
        $this->assertSame(5, Inspiration::pluck('slug')->unique()->count());
    }

    public function test_chaque_inspiration_a_un_seo_unique(): void
    {
        $this->seed(InspirationSeeder::class);

        $inspirations = Inspiration::all();

        foreach (['slug', 'title', 'meta_title', 'meta_description'] as $champ) {
            $this->assertSame(
                5,
                $inspirations->pluck($champ)->unique()->count(),
                "Le champ « {$champ} » doit être unique sur les cinq inspirations."
            );
        }

        foreach ($inspirations as $inspiration) {
            $this->assertLessThanOrEqual(70, mb_strlen($inspiration->meta_title));
            $this->assertLessThanOrEqual(160, mb_strlen($inspiration->meta_description));
            $this->assertNotEmpty($inspiration->resume);
        }
    }

    public function test_le_contenu_est_structure_et_ne_contient_aucun_html_dangereux(): void
    {
        $this->seed(InspirationSeeder::class);

        foreach (Inspiration::all() as $inspiration) {
            $html = $inspiration->description;

            $this->assertStringContainsString('<h2>', $html, 'Chaque article doit être découpé en sections.');
            $this->assertGreaterThan(1500, mb_strlen($html), 'Le contenu doit être suffisamment riche pour tester le front.');

            foreach (['<script', '<iframe', '<style', 'onerror=', 'onclick=', 'javascript:'] as $vecteur) {
                $this->assertStringNotContainsString($vecteur, $html);
            }

            // Aucune image ni aucun lien ne doit dépendre d'un service externe.
            $this->assertDoesNotMatchRegularExpression('#href="https?://#i', $html);

            // Le résumé sert aux cartes et aux métadonnées : il reste du texte.
            $this->assertStringNotContainsString('<', $inspiration->resume);
        }
    }

    public function test_les_images_referencent_des_fichiers_locaux_existants(): void
    {
        $this->seed(InspirationSeeder::class);

        $avecImage = 0;

        foreach (Inspiration::all() as $inspiration) {
            if (! $inspiration->image) {
                continue;
            }

            $avecImage++;
            $this->assertStringStartsNotWith('http', $inspiration->image, 'Aucune URL externe ne doit être stockée.');
            $this->assertTrue(
                Storage::disk('public')->exists($inspiration->image),
                "Le fichier « {$inspiration->image} » doit exister sur le disque public."
            );
        }

        $this->assertSame(5, $avecImage, 'Les cinq inspirations doivent avoir un visuel local.');
    }

    public function test_le_seeder_ne_remplace_pas_une_image_choisie_en_back_office(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('inspirations/choisie-en-admin.webp', 'contenu');

        Inspiration::create([
            'slug' => 'inspiration-salon-moderne-confortable',
            'title' => 'Titre existant',
            'image' => 'inspirations/choisie-en-admin.webp',
            'is_active' => true,
        ]);

        $this->seed(InspirationSeeder::class);

        $this->assertSame(
            'inspirations/choisie-en-admin.webp',
            Inspiration::where('slug', 'inspiration-salon-moderne-confortable')->value('image')
        );
    }

    private function admin(): User
    {
        Role::findOrCreate('superadmin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'admin', 'password' => bcrypt('secret')]
        );

        return $admin->hasRole('superadmin') ? $admin : tap($admin)->assignRole('superadmin');
    }

    /** Les inspirations seedées doivent être exploitables côté front et back-office. */
    public function test_les_inspirations_seedees_sont_visibles_sur_le_front_et_en_back_office(): void
    {
        Configuration::create([
            'site_name' => 'Paradis Déco',
            'support_email' => 'contact@example.test',
            'default_language' => 'fr',
            'currency' => 'TND',
            'shipping_cost' => 7.5,
            'free_shipping_threshold' => 120,
            'delivery_estimate_days' => 3,
            'maintenance_mode' => false,
        ]);

        $this->seed(InspirationSeeder::class);

        $listing = $this->get('/toutes/inspirations');
        $listing->assertOk();

        $article = Inspiration::where('slug', 'inspiration-salon-moderne-confortable')->firstOrFail();

        $page = $this->get('/inspiration/' . $article->slug);
        $page->assertOk();
        $page->assertSee($article->title);
        // Le contenu riche est rendu, pas échappé.
        $page->assertSee('<h2>', false);

        $admin = $this->actingAs($this->admin())->get('/admin/paradis-deco/inspirations');
        $admin->assertOk();

        $ajax = $this->actingAs($this->admin())->getJson('/admin/paradis-deco/ajax/get-inspiration');
        $ajax->assertOk();
        $this->assertSame(5, $ajax->json('recordsTotal') ?? count($ajax->json('data')));
    }
}
