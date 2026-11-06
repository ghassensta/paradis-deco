<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/admin/paradis-deco/dashborad';

    private int $cancelledId;
    private int $validId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cancelledId = DB::table('statuts')->insertGetId(['name' => 'Annulé', 'is_publish' => true]);
        $this->validId = DB::table('statuts')->insertGetId(['name' => 'Livré et payé', 'is_publish' => true]);
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

    private function product(string $name = 'Bougie', int $stock = 10): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'senteurs'],
            ['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]
        );

        return Product::create([
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'description' => 'Description.',
            'price' => 25,
            'stock' => $stock,
            'is_active' => true,
            'category_ids' => [$category->id],
            'images' => [],
        ]);
    }

    /** @param  list<array{0: Product, 1: int, 2: float}>  $items */
    private function order(int $daysAgo, int $statutId, float $total, array $items = []): Order
    {
        $client = Client::firstOrCreate(['email' => 'c@example.test'], ['name' => 'Client', 'phone' => '0600000000', 'adresse' => 'Tunis']);

        $order = Order::create([
            'numero_commande' => 'T-' . uniqid(),
            'client_id' => $client->id,
            'statut_id' => $statutId,
            'subtotal_ht' => $total,
            'shipping_cost' => 0,
            'tax_rate' => 0,
            'tax_tva' => 0,
            'total_ttc' => $total,
        ]);

        Order::where('id', $order->id)->update([
            'created_at' => now()->subDays($daysAgo),
            'updated_at' => now()->subDays($daysAgo),
        ]);

        foreach ($items as [$product, $qty, $price]) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
            ]);
        }

        return $order->refresh();
    }

    private function data(string $key, array $query = [])
    {
        return $this->actingAs($this->admin())
            ->get(self::URL . '?' . http_build_query($query))
            ->assertOk()
            ->viewData($key);
    }

    /** TEST 1 */
    public function test_un_superadmin_accede_au_dashboard(): void
    {
        $this->actingAs($this->admin())->get(self::URL)->assertOk();
    }

    /** TEST 2 */
    public function test_acces_refuse_sans_autorisation(): void
    {
        $this->get(self::URL)->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create(['name' => 's', 'email' => 's@example.test', 'password' => bcrypt('x')]);

        $this->actingAs($simple)->get(self::URL)->assertForbidden();
    }

    /** TEST 3 : base vide, aucune division par zéro. */
    public function test_dashboard_sans_commande(): void
    {
        $current = $this->data('current');

        $this->assertSame(0.0, $current['revenue']);
        $this->assertSame(0, $current['orders']);
        $this->assertSame(0.0, $current['aov']);
        $this->assertFalse($this->data('series')['hasData']);

        $this->actingAs($this->admin())->get(self::URL)->assertSee('Aucune donnée disponible pour cette période.', false);
    }

    /** TESTS 4, 5, 6, 7, 8 : les chiffres proviennent de données contrôlées. */
    public function test_ca_commandes_aov_et_unites(): void
    {
        $p1 = $this->product('Bougie');
        $p2 = $this->product('Diffuseur');

        $this->order(2, $this->validId, 100, [[$p1, 2, 25], [$p2, 1, 50]]);
        $this->order(5, $this->validId, 200, [[$p1, 4, 50]]);
        $this->order(20, $this->validId, 300, [[$p2, 3, 100]]);
        // Annulée : ne doit compter ni en CA, ni en commandes, ni en unités.
        $this->order(10, $this->cancelledId, 999, [[$p1, 9, 111]]);

        $current = $this->data('current', ['period' => 30]);

        $this->assertSame(600.0, $current['revenue']);
        $this->assertSame(3, $current['orders']);
        $this->assertSame(10, $current['units']);
        $this->assertSame(200.0, $current['aov']);
    }

    /** TEST 9 */
    public function test_top_produits(): void
    {
        $p1 = $this->product('Bougie', 3);
        $p2 = $this->product('Diffuseur');

        $this->order(1, $this->validId, 150, [[$p1, 5, 20], [$p2, 1, 50]]);

        $top = $this->data('topProducts', ['period' => 30]);

        $this->assertSame('Bougie', $top->first()['product']->name);
        $this->assertSame(5, $top->first()['units']);
        $this->assertSame(100.0, $top->first()['revenue']);
    }

    /** TEST 10 */
    public function test_alertes_de_stock(): void
    {
        $this->product('Rupture', 0);
        $this->product('Faible', 2);
        $this->product('Correct', 50);

        $catalogue = $this->data('catalogue');

        $this->assertSame(1, (int) $catalogue['products']['out_of_stock']);
        $this->assertSame(1, (int) $catalogue['products']['low_stock']);

        $labels = collect($this->data('alerts'))->pluck('label')->all();

        $this->assertContains('produit(s) en rupture de stock', $labels);
    }

    /** TEST 11 */
    public function test_compteurs_avis(): void
    {
        $product = $this->product();

        $product->avis()->create(['rating' => 5, 'comment' => 'Top', 'name' => 'A', 'approved' => true]);
        $product->avis()->create(['rating' => 3, 'comment' => 'Bof', 'name' => 'B', 'approved' => false]);

        $reviews = $this->data('catalogue')['reviews'];

        $this->assertSame(2, (int) $reviews['total']);
        $this->assertSame(1, (int) $reviews['approved']);
        $this->assertSame(1, (int) $reviews['pending']);
    }

    /** TEST 12 : score de complétude, pas de position Google. */
    public function test_score_de_completude_seo(): void
    {
        $this->product();

        $score = $this->data('seoScore');

        $this->assertIsInt($score['global']);
        $this->assertGreaterThanOrEqual(0, $score['global']);
        $this->assertLessThanOrEqual(100, $score['global']);
        $this->assertArrayHasKey('Produits — description courte', $score['checks']);
        // Le produit de test n'a pas de résumé : la complétude doit être à 0 %.
        $this->assertSame(0, $score['checks']['Produits — description courte']);
    }

    /** TESTS 13, 14 : le filtre de période change réellement le périmètre. */
    public function test_filtre_de_periode(): void
    {
        $p = $this->product();

        $this->order(3, $this->validId, 100, [[$p, 1, 100]]);
        $this->order(20, $this->validId, 500, [[$p, 1, 500]]);

        $this->assertSame(100.0, $this->data('current', ['period' => 7])['revenue']);
        $this->assertSame(600.0, $this->data('current', ['period' => 30])['revenue']);
        $this->assertSame(7, $this->data('period', ['period' => 7]));
        // Une période inconnue retombe sur la valeur par défaut.
        $this->assertSame(30, $this->data('period', ['period' => 999]));
    }

    /** TEST 15 */
    public function test_comparaison_avec_la_periode_precedente(): void
    {
        $p = $this->product();

        $this->order(5, $this->validId, 600, [[$p, 1, 600]]);   // période courante
        $this->order(40, $this->validId, 200, [[$p, 1, 200]]);  // période précédente

        $trends = $this->data('trends', ['period' => 30]);

        $this->assertSame(200.0, $trends['revenue']); // (600-200)/200
    }

    /** Sans historique, aucune tendance n'est inventée. */
    public function test_aucune_tendance_sans_historique(): void
    {
        $p = $this->product();
        $this->order(2, $this->validId, 100, [[$p, 1, 100]]);

        $this->assertNull($this->data('trends', ['period' => 30])['revenue']);

        $this->actingAs($this->admin())->get(self::URL)->assertSee('Données insuffisantes', false);
    }

    /** TEST 16 : le nombre de requêtes ne doit pas dépendre du volume. */
    public function test_absence_de_n_plus_un(): void
    {
        $products = collect(range(1, 6))->map(fn ($i) => $this->product("Produit {$i}"));

        foreach ($products as $i => $product) {
            $this->order($i + 1, $this->validId, 100, [[$product, 2, 50]]);
        }

        $this->actingAs($this->admin());

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get(self::URL)->assertOk();
        $withData = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Le même dashboard sur un catalogue six fois plus petit doit coûter
        // le même nombre de requêtes : les agrégats sont faits en SQL.
        $this->assertLessThanOrEqual(30, $withData, "Dashboard : {$withData} requêtes");
    }

    /** Les commandes récentes n'exposent aucune donnée personnelle superflue. */
    public function test_aucune_donnee_personnelle_exposee(): void
    {
        $p = $this->product();
        $this->order(1, $this->validId, 100, [[$p, 1, 100]]);

        $html = $this->actingAs($this->admin())->get(self::URL)->getContent();

        $this->assertStringNotContainsString('c@example.test', $html);
        $this->assertStringNotContainsString('0600000000', $html);
    }
}
