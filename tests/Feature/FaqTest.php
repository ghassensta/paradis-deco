<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Senteurs',
            'image' => 'x.webp',
            'is_active' => true,
            'description_courte' => 'Bougies et diffuseurs.',
        ]);
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

    private function product(): Product
    {
        return Product::create([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => 'Une bougie artisanale.',
            'price' => 25,
            'stock' => 5,
            'sku' => 'BOUG-1',
            'is_active' => true,
            'category_ids' => [$this->category->id],
            'images' => [],
        ]);
    }

    /** @return array<string, mixed> */
    private function productPayload(array $faqs): array
    {
        return [
            'name' => 'Bougie test',
            'price' => 25,
            'stock' => 5,
            'description' => 'Une bougie artisanale.',
            'category_ids' => [$this->category->id],
            'faqs' => $faqs,
        ];
    }

    private function faqSchema(string $html): ?array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        foreach ($m[1] as $json) {
            $decoded = json_decode($json, true);
            $this->assertNotNull($decoded, 'Chaque bloc JSON-LD doit rester valide.');

            if (($decoded['@type'] ?? null) === 'FAQPage') {
                return $decoded;
            }
        }

        return null;
    }

    public function test_creation_et_ordre_des_faq(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->productPayload([
                ['question' => 'Deuxième ?', 'answer' => 'Réponse B.', 'position' => 2, 'is_active' => 1],
                ['question' => 'Première ?', 'answer' => 'Réponse A.', 'position' => 1, 'is_active' => 1],
            ]))->assertSessionHasNoErrors();

        $faqs = $product->faqs()->get();

        $this->assertCount(2, $faqs);
        $this->assertSame('Première ?', $faqs->first()->question);
    }

    public function test_modification_et_suppression(): void
    {
        $product = $this->product();
        $faq = $product->faqs()->create(['question' => 'Ancienne ?', 'answer' => 'Ancienne réponse.', 'position' => 0, 'is_active' => true]);
        $product->faqs()->create(['question' => 'À supprimer ?', 'answer' => 'Bye.', 'position' => 1, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->productPayload([
                ['id' => $faq->id, 'question' => 'Modifiée ?', 'answer' => 'Nouvelle réponse.', 'position' => 0, 'is_active' => 1],
            ]))->assertSessionHasNoErrors();

        $this->assertSame(1, $product->faqs()->count());
        $this->assertSame('Modifiée ?', $faq->refresh()->question);
    }

    /** Une ligne sans question ou sans réponse n'est jamais enregistrée. */
    public function test_les_lignes_vides_sont_ignorees(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->productPayload([
                ['question' => 'Valide ?', 'answer' => 'Oui.', 'position' => 0, 'is_active' => 1],
                ['question' => '', 'answer' => 'Sans question', 'position' => 1, 'is_active' => 1],
                ['question' => 'Sans réponse ?', 'answer' => '', 'position' => 2, 'is_active' => 1],
            ]))->assertSessionHasNoErrors();

        $this->assertSame(1, $product->faqs()->count());
    }

    public function test_une_faq_inactive_n_est_pas_affichee(): void
    {
        $product = $this->product();
        $product->faqs()->create(['question' => 'Visible ?', 'answer' => 'Oui.', 'position' => 0, 'is_active' => true]);
        $product->faqs()->create(['question' => 'Masquée ?', 'answer' => 'Non.', 'position' => 1, 'is_active' => false]);

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('Visible ?', $html);
        $this->assertStringNotContainsString('Masquée ?', $html);

        $schema = $this->faqSchema($html);

        $this->assertNotNull($schema);
        $this->assertCount(1, $schema['mainEntity']);
    }

    /** Sans FAQ active, ni section ni FAQPage ne doivent apparaître. */
    public function test_aucune_faq_aucun_bloc(): void
    {
        $product = $this->product();

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('Questions fréquentes', $html);
        $this->assertNull($this->faqSchema($html));
    }

    public function test_le_json_ld_faq_ne_contient_pas_de_html(): void
    {
        $product = $this->product();
        $product->faqs()->create([
            'question' => 'Durée ?',
            'answer' => '<p>Environ <strong>50 heures</strong>.</p>',
            'position' => 0,
            'is_active' => true,
        ]);

        $schema = $this->faqSchema($this->get("/article/{$product->slug}")->getContent());

        $this->assertNotNull($schema);
        $this->assertSame('Environ 50 heures.', $schema['mainEntity'][0]['acceptedAnswer']['text']);
    }

    public function test_les_reponses_sont_nettoyees(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/produits/{$product->id}", $this->productPayload([
                [
                    'question' => 'XSS ?',
                    'answer' => '<p>Sûr</p><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">x</a>',
                    'position' => 0,
                    'is_active' => 1,
                ],
            ]))->assertSessionHasNoErrors();

        $stored = $product->faqs()->firstOrFail()->answer;

        foreach (['<script', 'onerror', 'javascript:', 'alert('] as $vector) {
            $this->assertStringNotContainsString($vector, $stored);
        }

        $html = $this->get("/article/{$product->slug}")->getContent();

        $this->assertStringNotContainsString('onerror', $html);
    }

    public function test_faq_categorie_affichee_et_structuree(): void
    {
        $this->product();

        $this->category->faqs()->create([
            'question' => 'Quels produits trouve-t-on ici ?',
            'answer' => 'Des bougies et des diffuseurs.',
            'position' => 0,
            'is_active' => true,
        ]);

        $html = $this->get("/collections/{$this->category->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('Questions fréquentes sur Senteurs', $html);

        $schema = $this->faqSchema($html);

        $this->assertNotNull($schema);
        $this->assertSame('Quels produits trouve-t-on ici ?', $schema['mainEntity'][0]['name']);
    }

    /** Les FAQ d'une catégorie et d'un produit ne se mélangent pas. */
    public function test_les_faq_sont_independantes(): void
    {
        $product = $this->product();
        $product->faqs()->create(['question' => 'Produit ?', 'answer' => 'A.', 'position' => 0, 'is_active' => true]);
        $this->category->faqs()->create(['question' => 'Catégorie ?', 'answer' => 'B.', 'position' => 0, 'is_active' => true]);

        $this->assertSame(1, $product->faqs()->count());
        $this->assertSame(1, $this->category->faqs()->count());
        $this->assertSame(2, Faq::count());

        $this->get("/article/{$product->slug}")->assertSee('Produit ?', false)->assertDontSee('Catégorie ?', false);
    }

    public function test_un_anonyme_ne_peut_pas_modifier_les_faq(): void
    {
        $product = $this->product();
        $product->faqs()->create(['question' => 'Originale ?', 'answer' => 'Oui.', 'position' => 0, 'is_active' => true]);

        $this->put("/admin/paradis-deco/produits/{$product->id}", $this->productPayload([
            ['question' => 'Piratée ?', 'answer' => 'Non.', 'position' => 0, 'is_active' => 1],
        ]))->assertRedirect('/admin/paradis-deco/login');

        $this->assertSame('Originale ?', $product->faqs()->firstOrFail()->question);
    }
}
