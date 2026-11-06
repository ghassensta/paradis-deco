<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Support\RichText;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductDescriptionsTest extends TestCase
{
    use RefreshDatabase;

    private Category $root;
    private Category $child;

    private const COURTE = 'Bougie artisanale eucalyptus & menthe, 50 h de combustion, cire de soja.';
    private const LONGUE = 'Coulée à la main en Tunisie, cette bougie associe une cire de soja naturelle à une mèche en coton non traité. Son parfum frais d\'eucalyptus et de menthe diffuse pendant environ 50 heures et convient aussi bien au salon qu\'à la salle de bain. Contenance 180 g, verre réutilisable après usage. Posez-la sur une surface plane, à l\'abri des courants d\'air, et laissez la cire fondre sur toute la surface au premier allumage pour éviter la formation d\'un tunnel.';

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');

        $this->root = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);
        $this->child = Category::create(['parent_id' => $this->root->id, 'name' => 'Bougies', 'is_active' => true]);
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

    private function product(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => self::LONGUE,
            'description_courte' => self::COURTE,
            'price' => 25,
            'stock' => 10,
            'sku' => 'BOUGIE-T1',
            'is_active' => true,
            'category_ids' => [$this->root->id, $this->child->id],
            'images' => [],
        ], $overrides));
    }

    /** @return array<string, mixed> */
    private function productSchema(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        foreach ($m[1] as $json) {
            $decoded = json_decode($json, true);
            $this->assertNotNull($decoded, 'Chaque bloc JSON-LD doit rester du JSON valide.');

            if (($decoded['@type'] ?? null) === 'Product') {
                return $decoded;
            }
        }

        $this->fail('Aucun JSON-LD Product trouvé.');
    }

    /** TEST 1 + 3 */
    public function test_creation_avec_les_deux_descriptions(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/produits', [
                'name' => 'Bougie test',
                'price' => 25,
                'stock' => 4,
                'description' => self::LONGUE,
                'description_courte' => self::COURTE,
                'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
                'category_ids' => [$this->child->id],
            ])
            ->assertRedirect(route('produits.index'));

        $product = Product::firstOrFail();

        $this->assertSame(self::COURTE, $product->description_courte);

        // La description longue est désormais normalisée en HTML par le
        // sanitizer : on compare le texte, pas le balisage octet par octet.
        $this->assertSame(self::LONGUE, RichText::toText($product->description));
        $this->assertStringStartsWith('<p>', $product->description);
    }

    /** TEST 2 */
    public function test_modification_des_deux_descriptions(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Bougie test',
                'price' => 30,
                'stock' => 2,
                'description' => 'Nouvelle description longue, suffisamment détaillée pour la fiche.',
                'description_courte' => 'Nouveau résumé court.',
                'category_ids' => [$this->child->id],
            ])
            ->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertSame('Nouveau résumé court.', $product->description_courte);
        $this->assertSame(
            'Nouvelle description longue, suffisamment détaillée pour la fiche.',
            RichText::toText($product->description)
        );
    }

    /** Un résumé vidé repasse à NULL plutôt qu'à une chaîne vide. */
    public function test_un_resume_vide_est_stocke_en_null(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Bougie test',
                'price' => 25,
                'stock' => 1,
                'description' => self::LONGUE,
                'description_courte' => '',
                'category_ids' => [$this->child->id],
            ])->assertSessionHasNoErrors();

        $this->assertNull($product->refresh()->description_courte);
    }

    /** TEST 6 */
    public function test_validation_du_resume(): void
    {
        $product = $this->product();

        // Trop long : refusé.
        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Bougie test',
                'price' => 25,
                'stock' => 1,
                'description' => self::LONGUE,
                'description_courte' => str_repeat('a', 601),
                'category_ids' => [$this->child->id],
            ])->assertSessionHasErrors('description_courte');

        // Absent : accepté, le champ reste facultatif pour les fiches existantes.
        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Bougie test',
                'price' => 25,
                'stock' => 1,
                'description' => self::LONGUE,
                'category_ids' => [$this->child->id],
            ])->assertSessionHasNoErrors();
    }

    /** TEST 4 */
    public function test_les_deux_descriptions_sont_affichees_sur_la_fiche(): void
    {
        $product = $this->product();

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertStringContainsString(e(self::COURTE), $html);
        $this->assertStringContainsString('Description de ' . e($product->name), $html);
        $this->assertStringContainsString(e(Str::limit(self::LONGUE, 60, '')), $html);
    }

    /** TEST 5 + 10 : fiche créée avant l'ajout du champ. */
    public function test_repli_lorsque_le_resume_est_absent(): void
    {
        $product = $this->product(['description_courte' => null]);

        $this->assertNotSame('', $product->short_description);
        $this->assertStringStartsWith('Coulée à la main', $product->short_description);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        // Le repli est affiché, et la section détaillée reste présente car la
        // description longue dépasse l'extrait de 300 caractères.
        $this->assertStringContainsString('Coulée à la main', $html);
        $this->assertStringContainsString('Description de ' . e($product->name), $html);
    }

    /** Pas de doublon quand la description longue tient entièrement dans l'extrait. */
    public function test_pas_de_section_dupliquee_pour_une_description_courte(): void
    {
        $product = $this->product([
            'description_courte' => null,
            'description' => 'Une description brève.',
        ]);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertFalse($product->hasDistinctLongDescription());
        $this->assertStringNotContainsString('Description de ' . e($product->name), $html);
        // Le texte figure aussi dans les métadonnées : on ne compte que le corps.
        $body = Str::after($html, '</head>');

        $this->assertSame(1, substr_count($body, 'Une description brève.'));
    }

    /** TEST 7 */
    public function test_le_json_ld_privilegie_le_resume(): void
    {
        $product = $this->product();

        $schema = $this->productSchema($this->get("/article/{$product->slug}")->getContent());

        $this->assertSame(self::COURTE, $schema['description']);
        $this->assertSame('BOUGIE-T1', $schema['sku']);
        $this->assertSame('25.00', $schema['offers']['price']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
        $this->assertArrayNotHasKey('aggregateRating', $schema);
    }

    public function test_le_json_ld_retombe_sur_la_description_longue(): void
    {
        $product = $this->product(['description_courte' => null]);

        $schema = $this->productSchema($this->get("/article/{$product->slug}")->getContent());

        $this->assertStringStartsWith('Coulée à la main', $schema['description']);
    }

    /** TEST 8 : la meta description reste pilotée par son propre champ. */
    public function test_la_meta_description_n_est_pas_le_resume(): void
    {
        $product = $this->product(['meta_description' => 'Meta rédigée à part, orientée clic.']);

        $html = $this->get("/article/{$product->slug}")->getContent();

        preg_match_all('#<meta name="description" content="(.*?)"#s', $html, $m);

        $this->assertCount(1, $m[1], 'Une seule balise meta description par page.');
        $this->assertSame('Meta rédigée à part, orientée clic.', $m[1][0]);
    }

    public function test_la_meta_description_retombe_sur_le_resume(): void
    {
        $product = $this->product(['meta_description' => null]);

        $html = $this->get("/article/{$product->slug}")->getContent();

        preg_match_all('#<meta name="description" content="(.*?)"#s', $html, $m);

        $this->assertCount(1, $m[1]);
        $this->assertStringContainsString('Bougie artisanale eucalyptus', html_entity_decode($m[1][0]));
    }

    /** TEST 9 + structure des titres. */
    public function test_la_fiche_conserve_un_seul_h1(): void
    {
        $product = $this->product();

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html));
        $this->assertGreaterThanOrEqual(1, preg_match_all('#<h2[\s>]#', $html));
    }

    /** TEST 11 */
    public function test_seul_un_superadmin_peut_modifier_les_descriptions(): void
    {
        $product = $this->product();

        $payload = [
            'name' => 'Bougie test',
            'price' => 25,
            'stock' => 1,
            'description' => 'Tentative.',
            'description_courte' => 'Piraté',
            'category_ids' => [$this->child->id],
        ];

        $this->put("/admin/paradis-deco/produits/{$product->id}", $payload)
            ->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create([
            'name' => 'simple',
            'email' => 'simple@example.test',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($simple)
            ->put("/admin/paradis-deco/produits/{$product->id}", $payload)
            ->assertForbidden();

        $this->assertSame(self::COURTE, $product->refresh()->description_courte);
    }
}
