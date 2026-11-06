<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductSkuTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');

        $this->category = Category::create([
            'name' => 'Senteurs',
            'image' => 'x.webp',
            'is_active' => true,
        ]);

        Role::findOrCreate('superadmin');

        $this->actingAs(tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin'));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Bougie test',
            'price' => 25,
            'stock' => 4,
            'description' => 'Une bougie.',
            'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'category_ids' => [$this->category->id],
        ], $overrides);
    }

    public function test_le_sku_est_enregistre_depuis_le_formulaire(): void
    {
        $this->post('/admin/paradis-deco/produits', $this->payload(['sku' => 'BOUGIE001']))
            ->assertRedirect(route('produits.index'));

        $this->assertSame('BOUGIE001', Product::firstOrFail()->sku);
    }

    public function test_un_sku_en_doublon_est_refuse(): void
    {
        $this->post('/admin/paradis-deco/produits', $this->payload(['sku' => 'BOUGIE001']));

        $this->post('/admin/paradis-deco/produits', $this->payload([
            'name' => 'Autre bougie',
            'sku' => 'BOUGIE001',
        ]))->assertSessionHasErrors('sku');

        $this->assertSame(1, Product::count());
    }

    /** Un SKU vide devient NULL : l'index unique interdirait plusieurs ''. */
    public function test_un_sku_vide_est_stocke_en_null(): void
    {
        $this->post('/admin/paradis-deco/produits', $this->payload(['sku' => '']));
        $this->post('/admin/paradis-deco/produits', $this->payload(['name' => 'Bougie 2', 'sku' => '']));

        $this->assertSame(2, Product::count());
        $this->assertSame(0, Product::whereNotNull('sku')->count());
    }

    public function test_le_sku_reste_modifiable_sur_le_produit_courant(): void
    {
        $product = Product::create([
            'name' => 'Ancien',
            'slug' => 'ancien',
            'description' => 'Test.',
            'price' => 10,
            'stock' => 1,
            'sku' => 'REF-1',
            'is_active' => true,
            'category_ids' => [$this->category->id],
            'images' => [],
        ]);

        $this->put("/admin/paradis-deco/produits/{$product->id}", [
            'name' => 'Ancien',
            'price' => 10,
            'stock' => 1,
            'sku' => 'REF-1',
            'description' => 'Test.',
            'category_ids' => [$this->category->id],
        ])->assertSessionHasNoErrors();

        $this->assertSame('REF-1', $product->refresh()->sku);
    }

    /** Le JSON-LD expose le vrai SKU, pas l'identifiant technique. */
    public function test_le_json_ld_expose_le_sku_reel(): void
    {
        $product = Product::create([
            'name' => 'Bougie',
            'slug' => 'bougie',
            'description' => 'Test.',
            'price' => 10,
            'stock' => 1,
            'sku' => 'REF-42',
            'is_active' => true,
            'category_ids' => [$this->category->id],
            'images' => [],
        ]);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        $schemas = array_map(fn ($json) => json_decode($json, true), $m[1]);

        foreach ($schemas as $schema) {
            $this->assertNotNull($schema, 'Chaque bloc JSON-LD doit être du JSON valide.');
        }

        $product_schema = collect($schemas)->firstWhere('@type', 'Product');

        $this->assertSame('REF-42', $product_schema['sku']);
    }
}
