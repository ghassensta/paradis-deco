<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductSlugTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Role::findOrCreate('superadmin');

        $this->admin = tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin');

        $this->category = Category::create(['name' => 'Déco', 'image' => 'x.webp', 'is_active' => true]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Lampe Dorée',
            'price' => 80,
            'stock' => 3,
            'description' => '<p>Une lampe.</p>',
            'category_ids' => [$this->category->id],
            'is_active' => 1,
        ], $overrides);
    }

    private function product(string $slug = 'lampe-doree'): Product
    {
        return Product::create([
            'name' => 'Lampe Dorée', 'slug' => $slug, 'description' => 'x', 'price' => 80, 'stock' => 3,
            'is_active' => true, 'category_ids' => [$this->category->id], 'images' => [],
        ]);
    }

    private function create(array $overrides = [])
    {
        return $this->actingAs($this->admin)->post('/admin/paradis-deco/produits', $this->payload($overrides + [
            'image_avant' => UploadedFile::fake()->image('cover.jpg', 800, 600),
        ]));
    }

    public function test_sans_slug_saisi_il_est_genere_depuis_le_nom(): void
    {
        $this->create()->assertSessionHasNoErrors();

        $this->assertSame('lampe-doree', Product::first()->slug);
    }

    public function test_le_slug_saisi_est_normalise(): void
    {
        $this->create(['slug' => '  Lampe Dorée 60 CM !  '])->assertSessionHasNoErrors();

        $this->assertSame('lampe-doree-60-cm', Product::first()->slug);
    }

    public function test_un_slug_deja_pris_est_refuse(): void
    {
        $this->product('lampe-de-chevet');

        $this->create(['name' => 'Autre lampe', 'slug' => 'Lampe de chevet'])->assertSessionHasErrors('slug');
    }

    public function test_le_slug_genere_ne_collisionne_pas(): void
    {
        $this->product('lampe-doree');
        Product::first()->update(['name' => 'Ancien nom']);

        $this->create()->assertSessionHasNoErrors();

        $this->assertSame('lampe-doree-2', Product::latest('id')->first()->slug);
    }

    public function test_modifier_le_slug_redirige_l_ancienne_adresse_en_301(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin)
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->payload(['slug' => 'lampe-doree-design']))
            ->assertSessionHasNoErrors();

        $this->assertSame('lampe-doree-design', $product->fresh()->slug);
        $this->get('/article/lampe-doree?variant=3')
            ->assertStatus(301)
            ->assertRedirect(route('preview-article', 'lampe-doree-design') . '?variant=3');
        $this->get('/article/lampe-doree-design')->assertOk();
    }

    public function test_revenir_a_un_ancien_slug_ne_cree_pas_de_boucle(): void
    {
        $product = $this->product();

        $product->changeSlug('lampe-doree-design');
        $product->changeSlug('lampe-doree');

        $this->get('/article/lampe-doree')->assertOk();
        $this->get('/article/lampe-doree-design')->assertRedirect(route('preview-article', 'lampe-doree'));
    }

    public function test_un_slug_vide_a_la_modification_garde_l_adresse(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin)
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->payload(['name' => 'Nouveau nom', 'slug' => '']))
            ->assertSessionHasNoErrors();

        $this->assertSame('lampe-doree', $product->fresh()->slug);
    }

    public function test_l_ancienne_adresse_d_un_produit_inactif_reste_404(): void
    {
        $product = $this->product();
        $product->changeSlug('lampe-doree-design');
        $product->update(['is_active' => false]);

        $this->get('/article/lampe-doree')->assertNotFound();
        $this->get('/article/inconnu')->assertNotFound();
    }

    public function test_le_champ_slug_est_present_dans_les_formulaires(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin)->get('/admin/paradis-deco/produits/create')->assertOk()->assertSee('name="slug"', false);
        $this->actingAs($this->admin)->get("/admin/paradis-deco/produits/{$product->id}/edit")
            ->assertOk()
            ->assertSee('value="lampe-doree"', false);
    }
}
