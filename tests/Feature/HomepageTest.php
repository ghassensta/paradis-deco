<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

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
            'city' => 'Sousse',
        ]);
    }

    private function category(string $name, ?string $image = 'categories/x.webp'): Category
    {
        return Category::create([
            'name' => $name,
            'image' => $image,
            'is_active' => true,
            'description_courte' => "Notre sélection {$name}.",
        ]);
    }

    private function product(string $name, Category $category, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Description.',
            'price' => 25,
            'stock' => 10,
            'is_active' => true,
            'category_ids' => [$category->id],
            'images' => [],
            'image_avant' => 'products/cover/x.webp',
        ], $overrides));
    }

    public function test_la_homepage_repond(): void
    {
        $this->get('/')->assertOk();
    }

    /** Les catégories affichées sont les catégories principales réelles. */
    public function test_les_univers_affichent_les_categories_racines(): void
    {
        $parent = $this->category('Senteurs');
        Category::create(['parent_id' => $parent->id, 'name' => 'Bougies', 'is_active' => true]);
        $this->category('Luminaires');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Explorez nos univers', $html);
        $this->assertStringContainsString('Senteurs', $html);
        $this->assertStringContainsString('Luminaires', $html);
        // Une sous-catégorie n'est pas un univers de premier niveau.
        $this->assertStringNotContainsString('>Bougies<', $html);
    }

    /** Aucune image externe ni cassée, même sans visuel de catégorie. */
    public function test_aucune_image_externe_ni_cassee(): void
    {
        $category = $this->category('Sans visuel', null);
        $this->product('Bougie', $category);

        $html = $this->get('/')->assertOk()->getContent();
        $body = Str::after($html, '</head>');

        $this->assertStringNotContainsString('via.placeholder.com', $html);
        $this->assertStringNotContainsString('src="/storage/"', $body);

        preg_match_all('#<img\b[^>]*>#', $body, $m);

        foreach ($m[0] as $img) {
            $this->assertStringContainsString('alt=', $img, "Image sans alt : {$img}");
        }
    }

    public function test_les_nouveautes_affichent_les_produits(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Bougie Eucalyptus', $category);

        $this->get('/')
            ->assertOk()
            ->assertSee('Nos dernières nouveautés', false)
            ->assertSee('Bougie Eucalyptus', false)
            ->assertSee('Ajouter au panier', false);
    }

    /** Sans vente, aucun classement n'est inventé. */
    public function test_les_incontournables_sont_masques_sans_vente(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Bougie', $category);

        $this->get('/')->assertOk()->assertDontSee('Nos incontournables', false);
    }

    /** Avec des ventes réelles, la section apparaît avec les bons produits. */
    public function test_les_incontournables_utilisent_les_ventes_reelles(): void
    {
        $statut = DB::table('statuts')->insertGetId(['name' => 'Livré et payé', 'is_publish' => true]);
        $category = $this->category('Senteurs');
        $vendu = $this->product('Produit vendu', $category);
        $this->product('Produit jamais vendu', $category);

        $client = Client::create(['name' => 'C', 'email' => 'c@example.test', 'phone' => '20123456', 'adresse' => 'Tunis']);
        $order = Order::create([
            'numero_commande' => 'CMD-TEST',
            'client_id' => $client->id,
            'statut_id' => $statut,
            'subtotal_ht' => 50,
            'shipping_cost' => 0,
            'tax_rate' => 0,
            'tax_tva' => 0,
            'total_ttc' => 50,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $vendu->id,
            'quantity' => 2,
            'unit_price' => 25,
            'subtotal' => 50,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Nos incontournables', $html);
        $this->assertStringContainsString('Produit vendu', $html);
    }

    /** Les sections sans données ne laissent aucun bloc vide. */
    public function test_aucune_section_vide(): void
    {
        $this->category('Senteurs');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('Inspirations Déco', $html);
        $this->assertStringNotContainsString('Ils Nous Ont Fait Confiance', $html);
        $this->assertStringNotContainsString('Nos incontournables', $html);
    }

    /** Aucune promesse commerciale non couverte par la configuration. */
    public function test_aucune_promesse_inventee(): void
    {
        $this->category('Senteurs');

        $html = $this->get('/')->assertOk()->getContent();

        foreach (['30 jours pour changer', 'retour gratuit', 'installons vos meubles', 'Garantie 2 ans'] as $claim) {
            $this->assertStringNotContainsString($claim, $html);
        }
    }

    /** Le seuil de livraison affiché provient de la configuration. */
    public function test_la_reassurance_suit_la_configuration(): void
    {
        $this->category('Senteurs');

        $this->get('/')->assertOk()->assertSee('Expédition depuis Sousse', false);

        Configuration::first()->update(['city' => null, 'free_shipping_threshold' => 0]);
        $this->app->forgetScopedInstances();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('Expédition depuis', $html);
        $this->assertStringNotContainsString('Livraison offerte', $html);
    }

    public function test_la_structure_seo_est_preservee(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Bougie', $category);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html), 'Un seul H1 sur la page.');
        $this->assertGreaterThanOrEqual(3, preg_match_all('#<h2[\s>]#', $html));
        $this->assertSame(1, preg_match_all('#<link rel="canonical"#', $html));
        $this->assertSame(1, preg_match_all('#<meta name="description"#', $html));

        // Le graphe JSON-LD doit rester valide.
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        $this->assertNotEmpty($m[1]);

        foreach ($m[1] as $json) {
            $this->assertNotNull(json_decode($json, true), 'JSON-LD invalide.');
        }
    }

    /** Une seule implémentation du panier, celle du layout. */
    public function test_une_seule_fonction_panier(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Bougie', $category);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#window\.addToCart\s*=|function addToCart\s*\(#', $html));
    }

    /** Les boutons portent les données attendues par la fonction globale. */
    public function test_les_boutons_panier_sont_complets(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Bougie', $category, ['price' => 42, 'stock' => 7]);

        $html = $this->get('/')->assertOk()->getContent();

        preg_match('#<button[^>]*?onclick="addToCart\(this\)"[^>]*?>#s', $html, $m);

        $this->assertNotEmpty($m, 'Aucun bouton d\'ajout au panier trouvé.');

        foreach (['data-id', 'data-name', 'data-price', 'data-image', 'data-stock', 'aria-label'] as $attribute) {
            $this->assertStringContainsString($attribute, $m[0]);
        }
    }

    /** Un produit épuisé n'est pas ajoutable. */
    public function test_un_produit_epuise_na_pas_de_bouton(): void
    {
        $category = $this->category('Senteurs');
        $this->product('Épuisé', $category, ['stock' => 0]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Indisponible', $html);
    }
}
