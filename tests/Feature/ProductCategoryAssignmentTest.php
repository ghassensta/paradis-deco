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

class ProductCategoryAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');
    }

    private function admin(): User
    {
        Role::findOrCreate('superadmin');

        return tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin');
    }

    /**
     * TEST 4 — une sous-catégorie ne peut pas être rattachée seule :
     * sa catégorie parente est ajoutée d'office, la hiérarchie reste cohérente.
     */
    public function test_selectionner_une_sous_categorie_rattache_aussi_son_parent(): void
    {
        $root = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);
        $child = Category::create(['parent_id' => $root->id, 'name' => 'Bougies', 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/produits', [
                'name' => 'Bougie test',
                'price' => 25,
                'stock' => 4,
                'description' => 'Une bougie.',
                'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
                'category_ids' => [$child->id],
            ])
            ->assertRedirect(route('produits.index'));

        $ids = Product::where('name', 'Bougie test')->firstOrFail()->category_ids;

        $this->assertContains($child->id, $ids);
        $this->assertContains($root->id, $ids, 'La catégorie parente doit être ajoutée automatiquement.');
    }

    /** Une catégorie inexistante est refusée par la validation serveur. */
    public function test_une_categorie_inexistante_est_refusee(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/produits', [
                'name' => 'Produit invalide',
                'price' => 10,
                'stock' => 1,
                'description' => 'Test.',
                'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
                'category_ids' => [99999],
            ])
            ->assertSessionHasErrors('category_ids.0');

        $this->assertDatabaseMissing('products', ['name' => 'Produit invalide']);
    }

    /** Le slug d'un produit reste stable : /article/{slug} est indexé. */
    public function test_le_slug_produit_ne_change_pas_au_renommage(): void
    {
        $root = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);

        $product = Product::create([
            'name' => 'Ancien nom',
            'slug' => 'ancien-nom',
            'description' => 'Test.',
            'price' => 10,
            'stock' => 1,
            'is_active' => true,
            'category_ids' => [$root->id],
            'images' => [],
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", [
                'name' => 'Nouveau nom',
                'price' => 12,
                'stock' => 2,
                'description' => 'Test modifié.',
                'category_ids' => [$root->id],
            ]);

        $product->refresh();

        $this->assertSame('Nouveau nom', $product->name);
        $this->assertSame('ancien-nom', $product->slug);
    }
}
