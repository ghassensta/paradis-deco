<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\RichText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductRichTextTest extends TestCase
{
    use RefreshDatabase;

    private Category $root;
    private Category $child;

    private const HTML_PROPRE = '<p>Lampe en <strong>rotin</strong> tressé.</p><h2>Caractéristiques</h2><ul><li>Hauteur 38 cm</li></ul><h3>Entretien</h3><p>Chiffon sec.</p>';

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');

        $this->root = Category::create(['name' => 'Luminaires', 'image' => 'x.webp', 'is_active' => true]);
        $this->child = Category::create(['parent_id' => $this->root->id, 'name' => 'Lampes', 'is_active' => true]);
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
            'name' => 'Lampe test',
            'slug' => 'lampe-test',
            'description' => self::HTML_PROPRE,
            'description_courte' => 'Lampe en rotin tressé, lumière douce pour un coin lecture.',
            'price' => 89.5,
            'stock' => 5,
            'sku' => 'LAMPE-T1',
            'is_active' => true,
            'category_ids' => [$this->root->id, $this->child->id],
            'images' => [],
        ], $overrides));
    }

    private function submit(array $overrides = [])
    {
        return $this->actingAs($this->admin())->post('/admin/paradis-deco/produits', array_merge([
            'name' => 'Lampe test',
            'price' => 89.5,
            'stock' => 5,
            'description' => self::HTML_PROPRE,
            'description_courte' => 'Résumé court.',
            'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'category_ids' => [$this->child->id],
        ], $overrides));
    }

    /** TESTS 1, 2, 3, 11 */
    public function test_creation_conserve_le_html_autorise(): void
    {
        $this->submit()->assertRedirect(route('produits.index'));

        $product = Product::firstOrFail();

        $this->assertSame('Résumé court.', $product->description_courte);

        foreach (['<p>', '<strong>', '<h2>', '<h3>', '<ul>', '<li>'] as $tag) {
            $this->assertStringContainsString($tag, $product->description);
        }
    }

    /** TEST 4 : le script est supprimé AVEC son contenu. */
    public function test_le_script_est_supprime(): void
    {
        $this->submit(['description' => '<p>Visible</p><script>alert("xss")</script>']);

        $stored = Product::firstOrFail()->description;

        $this->assertStringContainsString('<p>Visible</p>', $stored);
        $this->assertStringNotContainsString('script', $stored);
        $this->assertStringNotContainsString('alert', $stored);
    }

    /** TEST 5 */
    public function test_les_gestionnaires_d_evenements_sont_supprimes(): void
    {
        $this->submit(['description' => '<p onclick="alert(1)">Texte</p><img src=x onerror=alert(1)><div onload="x()">Bloc</div>']);

        $stored = Product::firstOrFail()->description;

        foreach (['onclick', 'onerror', 'onload', '<img'] as $vector) {
            $this->assertStringNotContainsString($vector, $stored);
        }

        // Le texte légitime survit.
        $this->assertStringContainsString('Texte', $stored);
        $this->assertStringContainsString('Bloc', $stored);
    }

    /** TEST 6 */
    public function test_les_urls_dangereuses_sont_supprimees(): void
    {
        $this->submit(['description' => '<a href="javascript:alert(1)">a</a><a href="data:text/html,x">b</a><a href="https://paradisdeco.com">ok</a>']);

        $stored = Product::firstOrFail()->description;

        $this->assertStringNotContainsString('javascript:', $stored);
        $this->assertStringNotContainsString('data:text/html', $stored);
        $this->assertStringContainsString('href="https://paradisdeco.com"', $stored);
        $this->assertStringContainsString('rel="noopener noreferrer"', $stored);
    }

    /** TEST 7 : rien de dangereux n'atteint la page publique. */
    public function test_le_front_ne_rend_aucun_vecteur_dangereux(): void
    {
        $product = $this->product([
            'description' => '<p>Sûr</p><script>alert(1)</script><img src=x onerror=alert(1)><iframe src="//evil"></iframe>',
        ]);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();
        $section = Str::between($html, 'class="product-description', '</section>');

        foreach (['<script', 'onerror', 'onclick', '<iframe', 'javascript:'] as $vector) {
            $this->assertStringNotContainsString($vector, $section);
        }

        $this->assertStringContainsString('Sûr', $section);
    }

    /** TEST 8 : un H1 saisi dans l'éditeur ne casse pas la hiérarchie. */
    public function test_un_h1_dans_la_description_ne_cree_pas_de_second_h1(): void
    {
        $product = $this->product(['description' => '<h1>Titre pirate</h1>' . self::HTML_PROPRE]);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html));
        $this->assertStringNotContainsString('Titre pirate</h1>', $html);
        // Le texte du titre est conservé, seule la balise disparaît.
        $this->assertStringContainsString('Titre pirate', $html);
    }

    /** TEST 9 */
    public function test_le_json_ld_ne_contient_pas_de_html(): void
    {
        $product = $this->product([
            'description_courte' => null,
            'description' => '<p>Texte <strong>riche</strong></p><script>alert(1)</script>',
        ]);

        $html = $this->get("/article/{$product->slug}")->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        $schema = null;

        foreach ($m[1] as $json) {
            $decoded = json_decode($json, true);
            $this->assertNotNull($decoded, 'Le JSON-LD doit rester valide.');

            if (($decoded['@type'] ?? null) === 'Product') {
                $schema = $decoded;
            }
        }

        $this->assertNotNull($schema);
        $this->assertSame('Texte riche', $schema['description']);
        $this->assertStringNotContainsString('<', $schema['description']);
        $this->assertStringNotContainsString('alert', $schema['description']);
        $this->assertSame('LAMPE-T1', $schema['sku']);
        $this->assertSame('89.50', $schema['offers']['price']);
    }

    /** TEST 10 */
    public function test_la_meta_description_ne_contient_pas_de_html(): void
    {
        $product = $this->product([
            'meta_description' => null,
            'description_courte' => null,
            'description' => '<p>Résumé <em>issu</em> du HTML</p><script>alert(1)</script>',
        ]);

        $html = $this->get("/article/{$product->slug}")->getContent();

        preg_match_all('#<meta name="description" content="(.*?)"#s', $html, $m);

        $this->assertCount(1, $m[1]);
        $this->assertSame('Résumé issu du HTML', html_entity_decode($m[1][0]));
    }

    /** TEST 12 */
    public function test_modification_via_le_formulaire(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Lampe test',
                'price' => 95,
                'stock' => 3,
                'description' => '<p>Nouvelle version</p><ol><li>Un</li></ol>',
                'description_courte' => 'Nouveau résumé.',
                'category_ids' => [$this->child->id],
            ])->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertStringContainsString('<ol><li>Un</li></ol>', $product->description);
        $this->assertSame('Nouveau résumé.', $product->description_courte);
    }

    /** TEST 13 : le contenu saisi survit à une erreur de validation. */
    public function test_le_contenu_est_restaure_apres_une_erreur(): void
    {
        $this->product(['name' => 'Déjà pris', 'slug' => 'deja-pris']);

        $this->submit([
            'name' => 'Déjà pris',
            'description' => '<p>À conserver</p><script>alert(1)</script>',
            'description_courte' => 'Résumé à conserver',
        ])->assertSessionHasErrors('name');

        $html = $this->actingAs($this->admin())
            ->get('/admin/paradis-deco/produits/create')
            ->assertOk()
            ->getContent();

        preg_match('#<div id="description-editor"[^>]*>(.*?)</div>#s', $html, $editor);
        preg_match('#id="description_courte"[^>]*>(.*?)</textarea>#s', $html, $courte);

        $this->assertStringContainsString('<p>À conserver</p>', $editor[1]);
        // La saisie renvoyée par old() est nettoyée avant réinjection.
        $this->assertStringNotContainsString('script', $editor[1]);
        $this->assertSame('Résumé à conserver', trim($courte[1]));
    }

    /** TEST 14 : ancienne fiche en texte brut. */
    public function test_une_description_texte_brut_reste_lisible(): void
    {
        $product = $this->product([
            'description_courte' => null,
            'description' => "Première ligne du texte historique.\n\nSecond paragraphe conservé tel quel, sans la moindre balise HTML à l'intérieur.",
        ]);

        $this->assertStringContainsString('<p>Première ligne', $product->safe_description);
        $this->assertStringContainsString('<p>Second paragraphe', $product->safe_description);

        $this->get("/article/{$product->slug}")
            ->assertOk()
            ->assertSee('Première ligne du texte historique.', false);
    }

    /** TEST 15 */
    public function test_un_produit_sans_description_longue_reste_affichable(): void
    {
        $product = $this->product(['description' => '', 'description_courte' => 'Uniquement un résumé.']);

        $this->assertSame('', $product->safe_description);
        $this->assertFalse($product->hasDistinctLongDescription());

        $this->get("/article/{$product->slug}")
            ->assertOk()
            ->assertSee('Uniquement un résumé.', false)
            ->assertDontSee('Description de ' . e($product->name), false);
    }

    /** TEST 16 : les deux champs restent distincts, sans duplication. */
    public function test_les_deux_descriptions_restent_distinctes(): void
    {
        $product = $this->product();

        $this->assertTrue($product->hasDistinctLongDescription());

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();
        $body = Str::after($html, '</head>');

        $this->assertSame(1, substr_count($body, 'Lampe en rotin tressé, lumière douce pour un coin lecture.'));
        $this->assertSame(1, substr_count($body, 'Description de ' . e($product->name)));
    }

    /** L'éditeur ne reçoit jamais de HTML doublement encodé. */
    public function test_l_editeur_ne_double_encode_pas_le_html(): void
    {
        $product = $this->product();

        $html = $this->actingAs($this->admin())
            ->get("/admin/paradis-deco/produits/{$product->id}/edit")
            ->assertOk()
            ->getContent();

        preg_match('#<div id="description-editor"[^>]*>(.*?)</div>#s', $html, $editor);

        $this->assertStringContainsString('<strong>rotin</strong>', $editor[1]);
        $this->assertStringNotContainsString('&lt;p&gt;', $editor[1]);
    }

    /** Le nettoyage ne dépend pas du navigateur : test unitaire du service. */
    public function test_le_service_de_nettoyage_couvre_les_vecteurs_connus(): void
    {
        $payloads = [
            '<script>alert(1)</script>' => '',
            '<img src=x onerror=alert(1)>' => '',
            '<a href="javascript:alert(1)">x</a>' => '<a rel="noopener noreferrer">x</a>',
            '<div onclick="alert(1)">texte</div>' => 'texte',
            '<iframe src="//evil"></iframe>' => '',
            '<style>body{display:none}</style>' => '',
            '<p><br></p>' => '',
        ];

        foreach ($payloads as $input => $expected) {
            $this->assertSame($expected, RichText::sanitize($input), "Payload : {$input}");
        }
    }
}
