<?php

namespace Tests\Feature;

use App\Mail\CheckoutConfirmMail;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('statuts')->insert([
            ['id' => 4, 'name' => 'En cours de traitement', 'is_publish' => false],
        ]);

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

        $category = Category::create(['name' => 'Senteurs', 'image' => 'x.webp', 'is_active' => true]);

        $this->product = Product::create([
            'name' => 'Bougie',
            'slug' => 'bougie',
            'description' => 'Une bougie.',
            'price' => 25,
            'stock' => 50,
            'is_active' => true,
            'category_ids' => [$category->id],
            'images' => [],
        ]);
    }

    private function submit(int $quantity = 2)
    {
        return $this->postJson('/order/submit', [
            'full_name' => 'Client Test',
            'phone' => '20123456',
            'address' => 'Tunis',
            'cart' => [[
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => 25,
                'quantity' => $quantity,
            ]],
        ]);
    }

    private function submitWithKey(?string $key)
    {
        return $this->postJson('/order/submit', [
            'full_name' => 'Client Test',
            'phone' => '20123456',
            'address' => 'Tunis',
            'checkout_key' => $key,
            'cart' => [['id' => $this->product->id, 'name' => 'Bougie', 'price' => 25, 'quantity' => 1]],
        ]);
    }

    public function test_une_meme_cle_ne_cree_qu_une_commande_et_un_mail(): void
    {
        Mail::fake();
        config(['mail.admin_to' => 'admin@example.test']);

        $first = $this->submitWithKey('tentative-1')->assertOk();
        $second = $this->submitWithKey('tentative-1')->assertOk();

        $this->assertSame(1, Order::count());
        $this->assertSame($first->json('order_reference'), $second->json('order_reference'));
        Mail::assertQueued(CheckoutConfirmMail::class, 1);
    }

    public function test_deux_cles_differentes_font_deux_commandes(): void
    {
        Mail::fake();

        $this->submitWithKey('tentative-1')->assertOk();
        $this->submitWithKey('tentative-2')->assertOk();

        $this->assertSame(2, Order::count());
    }

    public function test_le_rejeu_reussit_meme_si_le_stock_est_depuis_epuise(): void
    {
        Mail::fake();

        $variant = $this->product->variants()->create(['type' => 'Noir', 'price' => 25, 'stock' => 1]);
        $this->product->update(['has_variants' => true]);

        $payload = [
            'full_name' => 'Client Test',
            'phone' => '20123456',
            'address' => 'Tunis',
            'checkout_key' => 'tentative-1',
            'cart' => [['id' => $this->product->id, 'name' => 'Bougie', 'price' => 25, 'quantity' => 1, 'variant_id' => $variant->id]],
        ];

        $this->postJson('/order/submit', $payload)->assertOk();
        $variant->update(['stock' => 0]);
        $this->postJson('/order/submit', $payload)->assertOk();

        $this->assertSame(1, Order::count());
    }

    /** Sinon le client voit une erreur, réessaie et commande deux fois. */
    public function test_une_panne_de_mail_ne_fait_pas_echouer_la_commande(): void
    {
        config(['mail.admin_to' => 'admin@example.test']);
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP indisponible'));

        $this->submitWithKey('tentative-1')->assertOk();

        $this->assertSame(1, Order::count());
    }

    /** La référence permet au front de n'envoyer Purchase qu'une seule fois. */
    public function test_la_commande_renvoie_sa_reference(): void
    {
        Mail::fake();

        $response = $this->submit()->assertOk();

        $reference = $response->json('order_reference');

        $this->assertNotEmpty($reference);
        $this->assertSame($reference, Order::firstOrFail()->numero_commande);
    }

    /** Deux commandes distinctes ont deux références distinctes. */
    public function test_deux_commandes_ont_des_references_distinctes(): void
    {
        Mail::fake();

        $first = $this->submit()->json('order_reference');
        $second = $this->submit()->json('order_reference');

        $this->assertNotSame($first, $second);
    }

    /**
     * La notification admin passait par env('EMAIL_ADMIN'), absent du .env et
     * vidé par config:cache : le job de mail échouait faute de destinataire.
     */
    public function test_la_notification_admin_a_un_destinataire(): void
    {
        Mail::fake();

        $this->submit()->assertOk();

        $recipient = config('mail.admin_to');

        $this->assertNotEmpty($recipient, 'Un destinataire doit toujours être résolu.');

        Mail::assertQueued(
            CheckoutConfirmMail::class,
            fn (CheckoutConfirmMail $mail) => $mail->hasTo($recipient)
        );
    }

    /** Le serveur applique le seuil de livraison gratuite issu de la configuration. */
    public function test_le_seuil_de_livraison_est_applique_par_le_serveur(): void
    {
        Mail::fake();

        // 2 × 25 = 50 DT, sous le seuil de 120 : frais appliqués.
        $this->submit(2)->assertOk();
        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(7.5, (float) $order->shipping_cost);

        // 6 × 25 = 150 DT, au-dessus du seuil : livraison offerte.
        $this->submit(6)->assertOk();
        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(0.0, (float) $order->shipping_cost);
    }

    /**
     * Seuil à 0 = « pas de livraison offerte ». La comparaison brute
     * `$total >= $seuil` rendait au contraire la livraison gratuite dès
     * le premier dinar.
     */
    public function test_un_seuil_a_zero_n_offre_jamais_la_livraison(): void
    {
        Mail::fake();

        Configuration::first()->update(['free_shipping_threshold' => 0]);

        // Petit panier.
        $this->submit(2)->assertOk();
        $this->assertSame(7.5, (float) Order::latest('id')->firstOrFail()->shipping_cost);

        // Gros panier : les frais restent dus.
        $this->submit(40)->assertOk();
        $this->assertSame(7.5, (float) Order::latest('id')->firstOrFail()->shipping_cost);
    }

    public function test_une_commande_sans_panier_est_refusee(): void
    {
        Mail::fake();

        $this->postJson('/order/submit', [
            'full_name' => 'Client',
            'phone' => '20123456',
            'address' => 'Tunis',
            'cart' => [],
        ])->assertStatus(422);

        $this->assertSame(0, Order::count());
    }
}
