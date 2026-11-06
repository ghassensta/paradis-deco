<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductVariantExampleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Mail::fake();
        Role::findOrCreate('superadmin');

        $this->admin = tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin');

        DB::table('statuts')->insert([['id' => 4, 'name' => 'En cours', 'is_publish' => false]]);

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

        $this->category = Category::create(['name' => 'Déco', 'image' => 'x.webp', 'is_active' => true]);
    }

    private function product(array $variants): Product
    {
        $product = Product::create([
            'name' => 'Miroir',
            'slug' => 'miroir',
            'description' => 'Un miroir.',
            'price' => 100,
            'stock' => 1,
            'is_active' => true,
            'category_ids' => [$this->category->id],
            'images' => [],
        ]);

        foreach ($variants as $variant) {
            $product->variants()->create($variant + ['is_active' => true]);
        }

        $product->update(['has_variants' => $variants !== []]);

        return $product;
    }

    private function formPayload(array $variants, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Miroir',
            'price' => 100,
            'stock' => 3,
            'description' => '<p>Un miroir.</p>',
            'category_ids' => [$this->category->id],
            'is_active' => 1,
            'variants_present' => 1,
            'variants' => $variants,
        ], $overrides);
    }

    private function order(Product $product, array $line)
    {
        return $this->postJson('/order/submit', [
            'full_name' => 'Client Test',
            'phone' => '20123456',
            'address' => 'Tunis',
            'cart' => [array_merge([
                'id' => $product->id,
                'name' => $product->name,
                'price' => 1,
                'quantity' => 1,
            ], $line)],
        ]);
    }

    public function test_la_creation_enregistre_les_variantes_et_aligne_la_fiche(): void
    {
        $this->actingAs($this->admin)->post('/admin/paradis-deco/produits', $this->formPayload([
            ['type' => 'Noir', 'size' => '60 cm', 'price' => 150, 'stock' => 2, 'is_active' => 1,
                'image' => UploadedFile::fake()->image('noir.jpg', 800, 600)],
            ['type' => 'Bois', 'size' => '80 cm', 'price' => 120, 'stock' => 5, 'is_active' => 1],
            ['type' => 'Blanc', 'size' => '80 cm', 'price' => 90, 'stock' => 7, 'is_active' => 0],
            ['type' => '', 'size' => '', 'price' => '', 'stock' => 0, 'is_active' => 1],
        ], ['image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600)]))->assertSessionHasNoErrors();

        $product = Product::firstWhere('name', 'Miroir');

        $this->assertTrue($product->has_variants);
        $this->assertCount(3, $product->variants);
        // Les variantes inactives ne comptent ni dans le prix ni dans le stock.
        $this->assertEquals(120, $product->price);
        $this->assertSame(7, $product->stock);
        Storage::disk('public')->assertExists($product->variants[0]->image);
    }

    public function test_sans_prix_saisi_la_variante_reprend_celui_de_la_fiche(): void
    {
        $product = $this->product([]);

        $this->actingAs($this->admin)->put("/admin/paradis-deco/produits/{$product->id}", $this->formPayload([
            ['size' => 'L', 'price' => '', 'stock' => 4, 'is_active' => 1],
        ]))->assertRedirect();

        $this->assertEquals(100, $product->variants()->first()->price);
    }

    public function test_une_combinaison_en_double_est_refusee(): void
    {
        $this->actingAs($this->admin)->post('/admin/paradis-deco/produits', $this->formPayload([
            ['type' => 'Noir', 'size' => 'L', 'price' => 10, 'stock' => 1, 'is_active' => 1],
            ['type' => ' noir ', 'size' => 'l', 'price' => 12, 'stock' => 1, 'is_active' => 1],
        ], ['image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600)]))->assertSessionHasErrors('variants');

        $this->assertSame(0, Product::count());
    }

    public function test_des_lignes_heterogenes_sont_refusees(): void
    {
        $this->actingAs($this->admin)->post('/admin/paradis-deco/produits', $this->formPayload([
            ['type' => 'Noir', 'size' => '', 'price' => 10, 'stock' => 1, 'is_active' => 1],
            ['type' => '', 'size' => 'L', 'price' => 12, 'stock' => 1, 'is_active' => 1],
        ], ['image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600)]))->assertSessionHasErrors('variants');
    }

    public function test_retirer_toutes_les_variantes_revient_a_un_produit_simple(): void
    {
        $image = UploadedFile::fake()->image('noir.jpg', 800, 600)->store('products/variants', 'public');
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2, 'image' => $image]]);

        $this->actingAs($this->admin)
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->formPayload([]))
            ->assertRedirect();

        $product->refresh();

        $this->assertFalse($product->has_variants);
        $this->assertSame(0, $product->variants()->count());
        $this->assertEquals(100, $product->price);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_un_envoi_sans_marqueur_ne_supprime_aucune_variante(): void
    {
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2]]);

        $payload = $this->formPayload([]);
        unset($payload['variants_present'], $payload['variants']);

        $this->actingAs($this->admin)->put("/admin/paradis-deco/produits/{$product->id}", $payload);

        $this->assertSame(1, $product->variants()->count());
    }

    public function test_la_modification_met_a_jour_sans_toucher_aux_variantes_d_un_autre_produit(): void
    {
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2]]);
        $other = Product::create([
            'name' => 'Autre', 'slug' => 'autre', 'description' => 'x', 'price' => 5, 'stock' => 1,
            'is_active' => true, 'category_ids' => [$this->category->id], 'images' => [],
        ]);
        $foreign = $other->variants()->create(['type' => 'Rouge', 'price' => 5, 'stock' => 1]);

        $this->actingAs($this->admin)->put("/admin/paradis-deco/produits/{$product->id}", $this->formPayload([
            ['id' => $foreign->id, 'type' => 'Piraté', 'price' => 1, 'stock' => 1, 'is_active' => 1],
        ]));

        $this->assertSame('Rouge', $foreign->fresh()->type);
    }

    public function test_la_fiche_propose_les_options_et_une_offre_par_variante(): void
    {
        $product = $this->product([
            ['type' => 'Noir', 'size' => '60 cm', 'price' => 150, 'stock' => 0],
            ['type' => 'Bois', 'size' => '80 cm', 'price' => 120, 'stock' => 5],
        ]);

        $this->get("/article/{$product->slug}")
            ->assertOk()
            ->assertSee('id="variantPicker"', false)
            ->assertSee('data-value="Bois"', false)
            ->assertSee('data-value="60 cm"', false)
            // Présélection : la première variante disponible.
            ->assertSee('120.00')
            ->assertSee('https://schema.org/OutOfStock', false)
            ->assertSee('"name":"Bois / 80 cm"', false);
    }

    public function test_une_carte_de_variante_renvoie_vers_la_fiche(): void
    {
        $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2]]);

        $this->get('/toutes/produits')->assertOk()->assertSee('Choisir une option pour Miroir');
    }

    public function test_la_commande_reprend_le_prix_et_le_libelle_de_la_variante(): void
    {
        $product = $this->product([['type' => 'Noir', 'size' => 'L', 'price' => 150, 'stock' => 3]]);
        $variant = $product->variants()->first();

        $this->order($product, ['variant_id' => $variant->id, 'quantity' => 2])->assertOk();

        $item = Order::first()->items()->first();

        $this->assertEquals(150, $item->unit_price);
        $this->assertEquals(300, $item->subtotal);
        $this->assertSame($variant->id, $item->product_variant_id);
        $this->assertSame('Noir / L', $item->variant_label);
    }

    public function test_le_prix_envoye_par_le_navigateur_est_ignore(): void
    {
        $product = $this->product([]);

        $this->order($product, ['price' => 0.01])->assertOk();

        $this->assertEquals(100, Order::first()->items()->first()->unit_price);
    }

    public function test_une_variante_est_obligatoire_si_le_produit_en_a(): void
    {
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2]]);

        $this->order($product, [])->assertStatus(422);
        $this->assertSame(0, Order::count());
    }

    public function test_une_variante_d_un_autre_produit_est_refusee(): void
    {
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 2]]);
        $other = Product::create([
            'name' => 'Autre', 'slug' => 'autre', 'description' => 'x', 'price' => 5, 'stock' => 1,
            'is_active' => true, 'category_ids' => [$this->category->id], 'images' => [],
            'has_variants' => true,
        ]);
        $foreign = $other->variants()->create(['type' => 'Rouge', 'price' => 1, 'stock' => 9]);

        $this->order($product, ['variant_id' => $foreign->id])->assertStatus(422);
    }

    public function test_le_stock_de_la_variante_est_controle(): void
    {
        $product = $this->product([['type' => 'Noir', 'price' => 50, 'stock' => 1]]);

        $this->order($product, ['variant_id' => $product->variants()->first()->id, 'quantity' => 2])
            ->assertStatus(422);
    }

    public function test_un_article_supprime_renvoie_une_erreur_lisible(): void
    {
        $product = $this->product([]);
        $id = $product->id;
        $product->delete();

        $this->postJson('/order/submit', [
            'full_name' => 'Client Test', 'phone' => '20123456', 'address' => 'Tunis',
            'cart' => [['id' => $id, 'name' => 'Miroir', 'price' => 1, 'quantity' => 1]],
        ])->assertStatus(422);
    }

    public function test_le_llms_txt_liste_le_catalogue_actif(): void
    {
        $this->product([]);

        $this->get('/llms.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('## Produits')
            ->assertSee('[Miroir](' . route('preview-article', 'miroir') . ')', false);
    }

    public function test_le_seeder_d_exemples_est_rejouable_et_aligne_les_fiches(): void
    {
        Category::create(['name' => 'Luminaires', 'slug' => 'luminaires', 'image' => 'x.webp', 'is_active' => true]);

        $this->seed(ProductVariantExampleSeeder::class);
        $this->seed(ProductVariantExampleSeeder::class);

        $this->assertSame(3, Product::count());

        $lamp = Product::firstWhere('slug', 'lampe-de-table-design');

        $this->assertTrue($lamp->has_variants);
        $this->assertCount(10, $lamp->variants);
        $this->assertEquals(89, $lamp->price);
        $this->assertSame(1, $lamp->variants->where('stock', 0)->count());
        $this->get('/article/lampe-de-table-design')->assertOk()->assertSee('id="variantPicker"', false);
    }

    public function test_la_page_categorie_compte_les_produits_sans_afficher_de_balises(): void
    {
        $category = Category::create(['name' => 'Lampes', 'slug' => 'lampes', 'image' => 'x.webp', 'is_active' => true]);

        Product::create([
            'name' => 'Lampe', 'slug' => 'lampe', 'description' => '<p>Une belle lampe.</p>', 'price' => 5, 'stock' => 1,
            'is_active' => true, 'category_ids' => [$category->id], 'images' => [],
        ]);

        $response = $this->get('/collections/lampes')->assertOk()->assertDontSee('&lt;p&gt;', false);

        $this->assertSame(1, $response->viewData('categories')->firstWhere('slug', 'lampes')->products_count);
    }

    /** Régression : hors de la section, le script ne trouvait pas le gabarit. */
    public function test_les_gabarits_sont_dans_leur_editeur(): void
    {
        $html = $this->actingAs($this->admin)->get('/admin/paradis-deco/produits/create')->assertOk()->getContent();

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        foreach (['faq', 'variant'] as $editor) {
            $this->assertSame(1, $xpath->query(
                "//div[contains(@class, '{$editor}-editor')]//template[contains(@class, 'js-{$editor}-template')]"
            )->length, $editor);
        }
    }
}
