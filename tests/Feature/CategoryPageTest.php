<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Configuration;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    private Category $root;
    private Category $child;

    protected function setUp(): void
    {
        parent::setUp();

        Configuration::create(['site_name' => 'Paradis Déco']);

        $this->root = Category::create([
            'name' => 'Senteurs',
            'image' => 'categories/demo.webp',
            'is_active' => true,
        ]);

        $this->child = Category::create([
            'parent_id' => $this->root->id,
            'name' => 'Bougies parfumées',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => 'Une bougie de test.',
            'price' => 25,
            'stock' => 10,
            'is_active' => true,
            'category_ids' => [$this->root->id, $this->child->id],
            'images' => [],
        ]);
    }

    /** TEST 5 */
    public function test_une_page_categorie_fonctionne_sans_sous_categorie(): void
    {
        $seule = Category::create([
            'name' => 'Luminaires',
            'image' => 'categories/demo.webp',
            'is_active' => true,
        ]);

        $this->get("/collections/{$seule->slug}")
            ->assertOk()
            ->assertSee('Luminaires', false);
    }

    /** TEST 6 */
    public function test_une_page_sous_categorie_fonctionne(): void
    {
        $this->get("/collections/{$this->root->slug}/{$this->child->slug}")
            ->assertOk()
            ->assertSee('Bougies parfumées', false)
            ->assertSee('Bougie test', false);
    }

    /** TEST 7 */
    public function test_une_categorie_inexistante_retourne_404(): void
    {
        $this->get('/collections/categorie-qui-nexiste-pas')->assertNotFound();
    }

    /** TEST 8 */
    public function test_une_sous_categorie_inexistante_retourne_404(): void
    {
        $this->get("/collections/{$this->root->slug}/inexistante")->assertNotFound();

        // Une sous-catégorie valide sous le mauvais parent est également refusée.
        $autre = Category::create([
            'name' => 'Textile',
            'image' => 'categories/demo.webp',
            'is_active' => true,
        ]);

        $this->get("/collections/{$autre->slug}/{$this->child->slug}")->assertNotFound();
    }

    /** Une sous-catégorie atteinte en URL courte redirige vers l'URL canonique. */
    public function test_url_courte_d_une_sous_categorie_redirige_en_301(): void
    {
        $this->get("/collections/{$this->child->slug}")
            ->assertStatus(301)
            ->assertRedirect("/collections/{$this->root->slug}/{$this->child->slug}");
    }

    /** La page catégorie agrège les produits de ses sous-catégories. */
    public function test_une_categorie_principale_liste_les_produits_de_ses_enfants(): void
    {
        $this->get("/collections/{$this->root->slug}")
            ->assertOk()
            ->assertSee('Bougie test', false)
            ->assertSee($this->child->name, false);
    }

    /** Le fil d'Ariane structuré est présent et complet. */
    public function test_la_page_sous_categorie_expose_un_breadcrumblist(): void
    {
        $html = $this->get("/collections/{$this->root->slug}/{$this->child->slug}")->getContent();

        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
        $this->assertStringContainsString('"name":"Bougies parfum', $html);
    }
}
