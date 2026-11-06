<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Mail\ContactFormMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    /** TEST 11 — aucune configuration en base ne doit casser le front. */
    public function test_la_homepage_fonctionne_avec_une_base_vide(): void
    {
        $this->assertSame(0, \App\Models\Configuration::count());

        $this->get('/')->assertOk();
    }

    /** TEST 12 */
    public function test_le_formulaire_contact_envoie_un_mail(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Ghassen',
            'email' => 'ghassen@example.test',
            'phone' => '0600000000',
            'message' => 'Bonjour, une question sur vos bougies artisanales.',
        ])->assertRedirect(route('contact'));

        // Le destinataire est vérifié : une clé MAIL_CONTACT_TO présente mais
        // vide faisait échouer l'envoi sans que le test ne le détecte.
        Mail::assertSent(ContactFormMail::class, function (ContactFormMail $mail) {
            $this->assertNotEmpty(config('mail.contact_to'), 'Un destinataire doit toujours être résolu.');

            return $mail->hasTo(config('mail.contact_to'))
                && $mail->data['name'] === 'Ghassen';
        });
    }

    /** TEST 12 bis — la validation bloque un message trop court. */
    public function test_le_formulaire_contact_valide_les_champs(): void
    {
        Mail::fake();

        $this->post('/contact', ['name' => '', 'email' => 'pas-un-email', 'message' => 'court'])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    /** TEST 13 */
    public function test_le_depot_d_avis_fonctionne_et_reste_en_moderation(): void
    {
        $product = $this->product();

        $this->postJson('/avis/produit/store/', [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Très bon produit, je recommande.',
            'name' => 'Client',
            'location' => 'Tunis',
        ])->assertOk()->assertJson(['success' => true]);

        $avis = Avis::firstOrFail();

        $this->assertFalse($avis->approved, 'Un avis public doit être modéré avant publication.');
    }

    /** TEST 13 bis — le contrat JSON attendu par le JavaScript est respecté. */
    public function test_un_avis_invalide_renvoie_200_avec_les_erreurs(): void
    {
        $product = $this->product();

        $this->postJson('/avis/produit/store/', [
            'product_id' => $product->id,
            'rating' => 9,
            'comment' => '',
            'name' => '',
        ])
            ->assertOk()
            ->assertJson(['success' => false])
            ->assertJsonStructure(['success', 'errors' => ['rating', 'comment', 'name']]);

        $this->assertSame(0, Avis::count());
    }

    /** TEST 14 */
    public function test_une_facture_inexistante_retourne_404(): void
    {
        Role::findOrCreate('superadmin');
        $admin = tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin');

        $this->actingAs($admin)
            ->get('/admin/paradis-deco/commandes/99999/pdf')
            ->assertNotFound();
    }

    /** Le JSON-LD produit n'invente aucune note quand il n'y a pas d'avis. */
    public function test_aucun_aggregate_rating_sans_avis(): void
    {
        $product = $this->product();

        $html = $this->get("/article/{$product->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('aggregateRating', $html);
    }

    /** Le sitemap expose les catégories, sous-catégories et produits. */
    public function test_le_sitemap_liste_les_urls_du_catalogue(): void
    {
        $product = $this->product();
        $child = Category::whereNotNull('parent_id')->firstOrFail();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee($child->url, false)
            ->assertSee(route('preview-article', $product->slug), false);
    }

    private function product(): Product
    {
        $root = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);
        $child = Category::create(['parent_id' => $root->id, 'name' => 'Bougies', 'is_active' => true]);

        return Product::create([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => 'Une bougie de test.',
            'price' => 25,
            'stock' => 10,
            'is_active' => true,
            'category_ids' => [$root->id, $child->id],
            'images' => [],
        ]);
    }
}
