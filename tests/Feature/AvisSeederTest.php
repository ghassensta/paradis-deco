<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AvisSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AvisSeederTest extends TestCase
{
    use RefreshDatabase;

    /** Slugs réellement attendus par le seeder. */
    private const SLUGS = [
        'plaid-laine-naturelle',
        'tapis-kilim-vintage',
        'lampe-artisanale-rotin',
        'bougie-eucalyptus-menthe',
        'table-basse-industrielle',
    ];

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    private function catalogue(array $slugs = self::SLUGS): void
    {
        $category = Category::create(['name' => 'Décoration', 'image' => 'x.webp', 'is_active' => true]);

        foreach ($slugs as $slug) {
            Product::create([
                'name' => ucfirst(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'description' => 'Description du produit.',
                'price' => 50,
                'stock' => 10,
                'is_active' => true,
                'category_ids' => [$category->id],
                'images' => [],
            ]);
        }
    }

    public function test_le_seeder_cree_exactement_cinq_avis_approuves(): void
    {
        $this->catalogue();

        $this->seed(AvisSeeder::class);

        $this->assertSame(5, Avis::count());
        $this->assertSame(5, Avis::where('approved', true)->count());

        foreach (Avis::all() as $avis) {
            $this->assertContains($avis->rating, [1, 2, 3, 4, 5]);
            $this->assertNotEmpty($avis->comment);
            $this->assertNotEmpty($avis->name);
            // Un avis est du texte : aucune balise ne doit subsister.
            $this->assertStringNotContainsString('<', $avis->comment);
        }
    }

    public function test_le_seeder_est_idempotent(): void
    {
        $this->catalogue();

        $this->seed(AvisSeeder::class);
        $this->seed(AvisSeeder::class);
        $this->seed(AvisSeeder::class);

        $this->assertSame(5, Avis::count(), 'Rejouer le seeder ne doit pas dupliquer les avis.');
    }

    public function test_les_avis_sont_rattaches_a_des_produits_reels_sans_en_creer(): void
    {
        $this->catalogue();
        $avant = Product::count();

        $this->seed(AvisSeeder::class);

        $this->assertSame($avant, Product::count(), 'Le seeder ne doit créer aucun produit.');
        $this->assertSame(5, Avis::whereIn('product_id', Product::pluck('id'))->count());
        $this->assertSame(5, Avis::distinct()->count('product_id'), 'Chaque avis porte sur un produit différent.');
    }

    public function test_un_produit_absent_est_ignore_sans_erreur(): void
    {
        // Catalogue partiel : seuls trois des cinq produits attendus existent.
        $this->catalogue(array_slice(self::SLUGS, 0, 3));

        $this->seed(AvisSeeder::class);

        $this->assertSame(3, Avis::count(), 'Les avis sans produit correspondant sont ignorés.');
    }

    public function test_les_avis_approuves_alimentent_la_note_agregee_du_produit(): void
    {
        $this->catalogue();
        $this->seed(AvisSeeder::class);

        $reponse = $this->get('/article/lampe-artisanale-rotin');

        $reponse->assertOk();
        $reponse->assertSee('"aggregateRating"', false);
        $reponse->assertSee('"ratingValue":"4.0"', false);
        $reponse->assertSee('"reviewCount":1', false);
        $reponse->assertSee('Mariem K.');
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

    /** Les avis seedés remontent en back-office et dans les compteurs du tableau de bord. */
    public function test_les_avis_seedes_remontent_en_back_office(): void
    {
        $this->catalogue();
        $this->seed(AvisSeeder::class);

        $index = $this->actingAs($this->admin())->get('/admin/paradis-deco/avis');
        $index->assertOk();

        $ajax = $this->actingAs($this->admin())->getJson('/admin/paradis-deco/ajax/get-avis');
        $ajax->assertOk();
        $this->assertCount(5, $ajax->json('data'));

        $dashboard = $this->actingAs($this->admin())->get('/admin/paradis-deco/dashborad');
        $dashboard->assertOk();
    }

    /** Un avis en attente ne doit jamais compter dans la note publique. */
    public function test_un_avis_non_approuve_reste_hors_de_la_note_publique(): void
    {
        $this->catalogue();
        $this->seed(AvisSeeder::class);

        Avis::create([
            'product_id' => Product::where('slug', 'lampe-artisanale-rotin')->value('id'),
            'rating' => 1,
            'comment' => 'Avis en attente de modération.',
            'name' => 'Anonyme',
            'approved' => false,
        ]);

        $reponse = $this->get('/article/lampe-artisanale-rotin');

        $reponse->assertOk();
        $reponse->assertSee('"ratingValue":"4.0"', false);
        $reponse->assertSee('"reviewCount":1', false);
        $reponse->assertDontSee('Avis en attente de modération.');
    }
}
