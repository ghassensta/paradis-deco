<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\User;
use App\Support\TrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConfigurationAndTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('superadmin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'admin', 'password' => bcrypt('secret')]
        );

        return $admin->hasRole('superadmin') ? $admin : tap($admin)->assignRole('superadmin');
    }

    private function config(array $overrides = []): Configuration
    {
        return Configuration::create(array_merge([
            'site_name' => 'Paradis Déco',
            'support_email' => 'contact@example.test',
            'default_language' => 'fr',
            'currency' => 'TND',
            'shipping_cost' => 7.5,
            'free_shipping_threshold' => 120,
            'delivery_estimate_days' => 3,
            'maintenance_mode' => false,
        ], $overrides));
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'site_name' => 'Paradis Déco',
            'support_email' => 'contact@example.test',
            'default_language' => 'fr',
            'currency' => 'TND',
            'shipping_cost' => 7.5,
            'free_shipping_threshold' => 120,
            'delivery_estimate_days' => 3,
            'maintenance_mode' => 0,
        ], $overrides);
    }

    /* ===================== Configurations ===================== */

    /** Le dinar tunisien était refusé par la validation : la boutique vend en Tunisie. */
    public function test_la_devise_tunisienne_est_acceptee(): void
    {
        $config = $this->config(['currency' => 'USD']);

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/configurations/{$config->id}", $this->payload(['currency' => 'TND']))
            ->assertSessionHasNoErrors();

        $this->assertSame('TND', $config->refresh()->currency);
    }

    /** Toutes les valeurs, anciennes et nouvelles, survivent à un aller-retour. */
    public function test_toutes_les_configurations_persistent(): void
    {
        $config = $this->config();

        $values = [
            'site_name' => 'Paradis Déco Tunisie',
            'support_email' => 'hello@example.test',
            'shipping_cost' => 9.9,
            'free_shipping_threshold' => 200,
            'delivery_estimate_days' => 5,
            'support_phone' => '+216 20 123 456',
            'address' => '12 rue des Jasmins',
            'city' => 'Sousse',
            'working_hours' => 'Lun-Sam 9h-18h',
            'facebook_url' => 'https://facebook.com/paradisdeco.tn',
            'instagram_url' => 'https://instagram.com/paradisdeco',
            'tiktok_url' => 'https://tiktok.com/@paradisdeco',
            'whatsapp_number' => '+216 20 123 456',
            'meta_pixel_id' => '111222333444555',
            'meta_pixel_enabled' => 1,
        ];

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/configurations/{$config->id}", $this->payload($values))
            ->assertSessionHasNoErrors();

        $config->refresh();

        foreach ($values as $field => $expected) {
            $actual = $config->$field;
            $this->assertEquals($expected, is_bool($actual) ? (int) $actual : $actual, "Champ : {$field}");
        }
    }

    /** Une case décochée n'est pas postée : elle doit repasser à false. */
    public function test_decocher_un_tracker_le_desactive(): void
    {
        $config = $this->config(['meta_pixel_id' => '123', 'meta_pixel_enabled' => true]);

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/configurations/{$config->id}", $this->payload(['meta_pixel_id' => '123']))
            ->assertSessionHasNoErrors();

        $this->assertFalse($config->refresh()->meta_pixel_enabled);
    }

    public function test_seul_un_superadmin_modifie_la_configuration(): void
    {
        $config = $this->config();

        $this->put("/admin/paradis-deco/configurations/{$config->id}", $this->payload(['site_name' => 'Piraté']))
            ->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create(['name' => 's', 'email' => 's@example.test', 'password' => bcrypt('x')]);

        $this->actingAs($simple)
            ->put("/admin/paradis-deco/configurations/{$config->id}", $this->payload(['site_name' => 'Piraté']))
            ->assertForbidden();

        $this->assertSame('Paradis Déco', $config->refresh()->site_name);
    }

    /* ===================== Service de tracking ===================== */

    public function test_le_service_exige_identifiant_et_activation(): void
    {
        $config = $this->config();

        $this->assertNull(TrackingService::fromConfig($config)->idFor('meta'));

        $config->meta_pixel_enabled = true;
        $this->assertNull(TrackingService::fromConfig($config)->idFor('meta'), 'Activé sans identifiant : rien.');

        $config->meta_pixel_id = '123456789012345';
        $this->assertSame('123456789012345', TrackingService::fromConfig($config)->idFor('meta'));

        $config->meta_pixel_enabled = false;
        $this->assertNull(TrackingService::fromConfig($config)->idFor('meta'), 'Désactivé avec identifiant : rien.');
    }

    /* ===================== Injection dans les pages ===================== */

    public function test_aucun_pixel_quand_desactive(): void
    {
        $this->config(['meta_pixel_id' => '123456789012345', 'meta_pixel_enabled' => false]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('connect.facebook.net', $html);
        $this->assertStringNotContainsString('fbq(', $html);
    }

    public function test_aucun_pixel_quand_identifiant_vide(): void
    {
        $this->config(['meta_pixel_id' => '', 'meta_pixel_enabled' => true]);

        $this->get('/')->assertOk()->assertDontSee('connect.facebook.net', false);
    }

    public function test_le_pixel_est_injecte_quand_active(): void
    {
        $this->config(['meta_pixel_id' => '123456789012345', 'meta_pixel_enabled' => true]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('connect.facebook.net', $html);
        $this->assertStringContainsString('123456789012345', $html);
        // Chargé derrière la porte de consentement.
        $this->assertStringContainsString('paradisConsent.run', $html);
    }

    public function test_changer_l_identifiant_change_le_script(): void
    {
        $config = $this->config(['meta_pixel_id' => '111111111111111', 'meta_pixel_enabled' => true]);

        $this->get('/')->assertSee('111111111111111', false);

        $config->update(['meta_pixel_id' => '999999999999999']);

        // La configuration est résolue une fois par requête (binding scoped) :
        // on simule la requête suivante, comme le fait Octane entre deux appels.
        $this->app->forgetScopedInstances();

        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('999999999999999', $html);
        $this->assertStringNotContainsString('111111111111111', $html);
    }

    /** Le tracking ne doit jamais apparaître dans l'administration. */
    public function test_aucun_pixel_dans_l_administration(): void
    {
        $this->config(['meta_pixel_id' => '123456789012345', 'meta_pixel_enabled' => true]);

        $html = $this->actingAs($this->admin())
            ->get('/admin/paradis-deco/configurations')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('connect.facebook.net', $html);
    }

    /** Sans tracker actif, la fonction reste définie : aucune erreur JS côté front. */
    public function test_paradis_track_toujours_defini(): void
    {
        $this->config();

        $this->get('/')->assertOk()->assertSee('window.paradisTrack', false);
    }

    /* ===================== Effet réel sur le front ===================== */

    /** Le seuil était codé en dur à 150 : le réglage n'avait aucun effet. */
    public function test_le_seuil_de_livraison_vient_de_la_configuration(): void
    {
        $this->config(['free_shipping_threshold' => 175]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('FREE_SHIPPING_MIN = 175', $html);
        $this->assertStringNotContainsString('FREE_SHIPPING_MIN = 150', $html);
        $this->assertStringContainsString('id="freeShippingBlock"', $html);
    }

    public function test_sans_seuil_aucune_barre_de_progression(): void
    {
        $this->config(['free_shipping_threshold' => 0]);

        $html = $this->get('/')->assertOk()->getContent();

        // L'identifiant apparaît aussi dans le script de rendu : on vérifie
        // l'absence de l'élément lui-même, pas de la chaîne.
        $this->assertStringNotContainsString('id="freeShippingBlock"', $html);
    }

    /** Les coordonnées configurées alimentent réellement le pied de page. */
    public function test_les_coordonnees_alimentent_le_pied_de_page(): void
    {
        $this->config([
            'support_phone' => '+216 20 123 456',
            'address' => '12 rue des Jasmins',
            'facebook_url' => 'https://facebook.com/paradisdeco.tn',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('+216 20 123 456', false)
            ->assertSee('12 rue des Jasmins', false)
            ->assertSee('https://facebook.com/paradisdeco.tn', false);
    }
}
