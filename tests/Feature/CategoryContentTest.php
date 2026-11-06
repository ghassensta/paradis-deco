<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryContentTest extends TestCase
{
    use RefreshDatabase;

    private const LONGUE = '<p>Notre sélection <strong>senteurs</strong> réunit bougies et diffuseurs.</p><h2>Comment choisir</h2><ul><li>Selon la pièce</li><li>Selon l\'intensité</li></ul>';

    private function admin(): User
    {
        Role::findOrCreate('superadmin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'admin', 'password' => bcrypt('secret')]
        );

        return $admin->hasRole('superadmin') ? $admin : tap($admin)->assignRole('superadmin');
    }

    private function category(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Senteurs',
            'image' => 'x.webp',
            'is_active' => true,
            'description_courte' => 'Bougies et diffuseurs pour une atmosphère chaleureuse.',
            'description' => self::LONGUE,
        ], $overrides));
    }

    private function withProduct(Category $category): Product
    {
        return Product::create([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => 'Une bougie.',
            'price' => 25,
            'stock' => 5,
            'is_active' => true,
            'category_ids' => [$category->id],
            'images' => [],
        ]);
    }

    public function test_enregistrement_des_deux_descriptions(): void
    {
        $category = $this->category(['description' => null, 'description_courte' => null]);

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/categories/{$category->id}", [
                'name' => 'Senteurs',
                'is_active' => 1,
                'description_courte' => 'Résumé de la catégorie.',
                'description' => self::LONGUE,
            ])->assertOk();

        $category->refresh();

        $this->assertSame('Résumé de la catégorie.', $category->description_courte);
        $this->assertStringContainsString('<h2>Comment choisir</h2>', $category->description);
        $this->assertStringContainsString('<li>Selon la pièce</li>', $category->description);
    }

    public function test_le_html_dangereux_est_nettoye(): void
    {
        $category = $this->category();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/categories/{$category->id}", [
                'name' => 'Senteurs',
                'is_active' => 1,
                'description' => '<h1>H1</h1><p>Sûr</p><script>alert(1)</script><img src=x onerror=alert(1)>'
                    . '<a href="javascript:alert(1)">x</a><iframe src="//evil"></iframe><div onclick="alert(1)">d</div>',
            ])->assertOk();

        $stored = $category->refresh()->description;

        foreach (['<script', 'onerror', 'onclick', 'javascript:', '<iframe', '<h1', 'alert('] as $vector) {
            $this->assertStringNotContainsString($vector, $stored, "Vecteur non filtré : {$vector}");
        }

        $this->assertStringContainsString('<p>Sûr</p>', $stored);
    }

    public function test_le_front_affiche_les_deux_descriptions(): void
    {
        $category = $this->category();
        $this->withProduct($category);

        $html = $this->get("/collections/{$category->slug}")->assertOk()->getContent();
        $body = Str::after($html, '</head>');

        $this->assertStringContainsString('Bougies et diffuseurs', $body);
        $this->assertStringContainsString('Comment choisir', $body);
        $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html));
    }

    public function test_le_front_ne_rend_aucun_vecteur_dangereux(): void
    {
        $category = $this->category(['description' => '<p>Sûr</p><script>alert(1)</script>']);
        $this->withProduct($category);

        $html = $this->get("/collections/{$category->slug}")->assertOk()->getContent();
        $section = Str::between($html, 'description-categorie', '</section>');

        foreach (['<script', 'onerror', 'javascript:'] as $vector) {
            $this->assertStringNotContainsString($vector, $section);
        }
    }

    /** La meta description reste pilotée par son champ, sans HTML. */
    public function test_la_meta_categorie_est_du_texte_brut(): void
    {
        $category = $this->category(['meta_description' => null]);
        $this->withProduct($category);

        $html = $this->get("/collections/{$category->slug}")->getContent();

        preg_match_all('#<meta name="description" content="(.*?)"#s', $html, $m);

        $this->assertCount(1, $m[1]);
        $this->assertStringNotContainsString('<', $m[1][0]);
        $this->assertStringContainsString('Bougies et diffuseurs', html_entity_decode($m[1][0]));
    }

    /** Pas de section dupliquée quand le repli restitue déjà tout le texte. */
    public function test_pas_de_section_longue_dupliquee(): void
    {
        $category = $this->category(['description_courte' => null, 'description' => 'Texte bref.']);
        $this->withProduct($category);

        $this->assertFalse($category->hasDistinctLongDescription());

        $html = $this->get("/collections/{$category->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('bien choisir', $html);
    }

    public function test_seul_un_superadmin_peut_modifier_une_categorie(): void
    {
        $category = $this->category();

        $this->put("/admin/paradis-deco/categories/{$category->id}", ['name' => 'Piraté', 'is_active' => 1])
            ->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create(['name' => 's', 'email' => 's@example.test', 'password' => bcrypt('x')]);

        $this->actingAs($simple)
            ->put("/admin/paradis-deco/categories/{$category->id}", ['name' => 'Piraté', 'is_active' => 1])
            ->assertForbidden();

        $this->assertSame('Senteurs', $category->refresh()->name);
    }
}
