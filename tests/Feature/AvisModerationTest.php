<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AvisModerationTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

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

        $category = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);

        $this->product = Product::create([
            'name' => 'Bougie',
            'slug' => 'bougie',
            'description' => 'Une bougie.',
            'price' => 25,
            'stock' => 10,
            'is_active' => true,
            'category_ids' => [$category->id],
            'images' => [],
        ]);
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

    private function avis(array $overrides = []): Avis
    {
        return Avis::create(array_merge([
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Produit conforme à la description.',
            'name' => 'Client',
            'approved' => false,
        ], $overrides));
    }

    public function test_index_admin_reserve_au_superadmin(): void
    {
        // L'anonyme d'abord : actingAs persiste ensuite sur toute la requête.
        $this->get('/admin/paradis-deco/avis')->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create(['name' => 's', 'email' => 's@example.test', 'password' => bcrypt('x')]);
        $this->actingAs($simple)->get('/admin/paradis-deco/avis')->assertForbidden();

        $this->actingAs($this->admin())->get('/admin/paradis-deco/avis')->assertOk();
    }

    /** Un avis déposé arrive toujours en attente de modération. */
    public function test_un_avis_depose_est_en_attente(): void
    {
        $this->postJson('/avis/produit/store/', [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Très bon produit, conforme.',
            'name' => 'Client',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertFalse(Avis::firstOrFail()->approved);
    }

    /** Un avis en attente n'apparaît nulle part publiquement. */
    public function test_un_avis_en_attente_est_invisible(): void
    {
        $this->avis(['comment' => 'Commentaire en attente de validation']);

        $product = $this->get("/article/{$this->product->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('Commentaire en attente de validation', $product);
        $this->assertStringNotContainsString('aggregateRating', $product);

        $home = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Commentaire en attente de validation', $home);
    }

    /** Publié : visible et compté dans les données structurées. */
    public function test_un_avis_publie_est_visible(): void
    {
        $this->avis(['comment' => 'Commentaire publié', 'approved' => true]);

        $html = $this->get("/article/{$this->product->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('Commentaire publié', $html);
        $this->assertStringContainsString('aggregateRating', $html);
    }

    /** Dépublié : il disparaît, et la note agrégée avec lui. */
    public function test_un_avis_depublie_disparait(): void
    {
        $avis = $this->avis(['comment' => 'Commentaire publié', 'approved' => true]);

        $this->get("/article/{$this->product->slug}")->assertSee('Commentaire publié', false);

        $avis->update(['approved' => false]);

        $html = $this->get("/article/{$this->product->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('Commentaire publié', $html);
        $this->assertStringNotContainsString('aggregateRating', $html);
    }

    public function test_suppression_par_l_admin(): void
    {
        $avis = $this->avis();

        $this->actingAs($this->admin())
            ->deleteJson("/admin/paradis-deco/avis/{$avis->id}")
            ->assertOk();

        $this->assertSame(0, Avis::count());
    }

    public function test_un_anonyme_ne_peut_pas_supprimer_un_avis(): void
    {
        $avis = $this->avis();

        $this->delete("/admin/paradis-deco/avis/{$avis->id}")->assertRedirect('/admin/paradis-deco/login');

        $this->assertSame(1, Avis::count());
    }

    /** Le commentaire est stocké en texte : aucune balise conservée. */
    public function test_le_commentaire_est_stocke_en_texte(): void
    {
        $this->postJson('/avis/produit/store/', [
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => '<script>alert(1)</script>Excellent produit vraiment',
            'name' => '<img src=x onerror=alert(1)>Client',
        ])->assertOk();

        $avis = Avis::firstOrFail();

        $this->assertSame('Excellent produit vraiment', $avis->comment);
        $this->assertSame('Client', $avis->name);
    }

    /** Le front échappe le commentaire, y compris pour une ligne héritée. */
    public function test_le_front_neutralise_un_commentaire_herite(): void
    {
        $this->avis(['comment' => '<script>alert(1)</script>Hérité', 'approved' => true]);

        $body = Str::after($this->get("/article/{$this->product->slug}")->getContent(), '</head>');

        $this->assertStringNotContainsString('<script>alert(1)</script>Hérité', $body);
        $this->assertStringContainsString('Hérité', $body);
    }

    /** L'endpoint d'administration échappe les valeurs réinjectées en HTML. */
    public function test_l_endpoint_admin_echappe_les_commentaires(): void
    {
        $this->avis(['comment' => '<script>alert(1)</script>Hérité']);

        $data = $this->actingAs($this->admin())
            ->getJson('/admin/paradis-deco/ajax/get-avis?draw=1&start=0&length=10')
            ->assertOk()
            ->json('data');

        $this->assertStringContainsString('&lt;script&gt;', $data[0]['comment']);
        $this->assertStringNotContainsString('<script>', $data[0]['comment']);
    }

    /** La note agrégée ne compte que les avis approuvés. */
    public function test_la_note_agregee_ignore_les_avis_en_attente(): void
    {
        $this->avis(['rating' => 5, 'approved' => true]);
        $this->avis(['rating' => 1, 'approved' => false]);

        $html = $this->get("/article/{$this->product->slug}")->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        $schema = null;

        foreach ($m[1] as $json) {
            $decoded = json_decode($json, true);

            if (($decoded['@type'] ?? null) === 'Product') {
                $schema = $decoded;
            }
        }

        $this->assertNotNull($schema);
        $this->assertSame('5.0', $schema['aggregateRating']['ratingValue']);
        $this->assertSame(1, $schema['aggregateRating']['reviewCount']);
    }
}
