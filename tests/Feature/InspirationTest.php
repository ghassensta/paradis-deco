<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\Inspiration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InspirationTest extends TestCase
{
    use RefreshDatabase;

    private const XSS = '<h2>Ambiance</h2><p>Tons <strong>chauds</strong>.</p><script>alert("xss")</script>'
        . '<img src=x onerror=alert(1)><iframe src="//evil"></iframe><div onclick="alert(1)">d</div><h1>H1</h1>';

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');

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

    private function image(int $width = 1200, int $height = 800): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        imagefilledrectangle($gd, 0, 0, $width, $height, imagecolorallocate($gd, 200, 170, 130));
        $path = sys_get_temp_dir() . '/insp-' . uniqid() . '.jpg';
        imagejpeg($gd, $path, 90);
        imagedestroy($gd);

        return new UploadedFile($path, 'photo.jpg', null, null, true);
    }

    private function inspiration(array $overrides = []): Inspiration
    {
        return Inspiration::create(array_merge([
            'title' => 'Salon cosy',
            'slug' => 'salon-cosy',
            'resume' => 'Une ambiance chaleureuse.',
            'description' => '<p>Contenu</p>',
            'is_active' => true,
        ], $overrides));
    }

    public function test_index_admin_accessible_au_superadmin(): void
    {
        $this->actingAs($this->admin())->get('/admin/paradis-deco/inspirations')->assertOk();
    }

    public function test_acces_refuse_sans_autorisation(): void
    {
        $this->get('/admin/paradis-deco/inspirations')->assertRedirect('/admin/paradis-deco/login');

        $simple = User::create(['name' => 's', 'email' => 's@example.test', 'password' => bcrypt('x')]);

        $this->actingAs($simple)->get('/admin/paradis-deco/inspirations')->assertForbidden();
    }

    /** Création complète : image convertie, contenu nettoyé, slug généré. */
    public function test_creation_avec_image_et_contenu_riche(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/inspirations', [
                'title' => 'Salon Cosy Test',
                'resume' => 'Ambiance chaleureuse<script>alert(1)</script>',
                'description' => self::XSS,
                'is_active' => 1,
                'image' => $this->image(3000, 2000),
            ])->assertRedirect(route('inspirations.index'));

        $inspiration = Inspiration::firstOrFail();

        $this->assertSame('salon-cosy-test', $inspiration->slug);
        $this->assertStringEndsWith('.webp', $inspiration->image);
        $this->assertTrue(Storage::disk('public')->exists($inspiration->image));

        [$width] = getimagesize(Storage::disk('public')->path($inspiration->image));
        $this->assertSame(1600, $width, 'Une image de 3000 px doit être réduite.');

        // Contenu autorisé conservé, vecteurs supprimés.
        $this->assertStringContainsString('<h2>Ambiance</h2>', $inspiration->description);
        $this->assertStringContainsString('<strong>chauds</strong>', $inspiration->description);

        foreach (['<script', 'onerror', 'onclick', '<iframe', '<h1', 'alert('] as $vector) {
            $this->assertStringNotContainsString($vector, $inspiration->description, "Vecteur : {$vector}");
        }

        // Le résumé est du texte, jamais du balisage.
        $this->assertSame('Ambiance chaleureuse', $inspiration->resume);
    }

    public function test_un_fichier_non_image_est_refuse(): void
    {
        $path = sys_get_temp_dir() . '/evil-' . uniqid() . '.jpg';
        file_put_contents($path, "<?php echo 'pwned'; ?>");

        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/inspirations', [
                'title' => 'Evil',
                'is_active' => 1,
                'image' => new UploadedFile($path, 'evil.jpg', null, null, true),
            ])->assertSessionHasErrors('image');

        $this->assertSame(0, Inspiration::count());
    }

    public function test_une_image_trop_petite_est_refusee(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/inspirations', [
                'title' => 'Trop petite',
                'is_active' => 1,
                'image' => $this->image(100, 100),
            ])->assertSessionHasErrors('image');
    }

    /** Le remplacement d'image ne laisse pas de fichier orphelin. */
    public function test_modification_remplace_limage(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/inspirations', [
                'title' => 'Salon', 'is_active' => 1, 'image' => $this->image(),
            ]);

        $inspiration = Inspiration::firstOrFail();
        $old = $inspiration->image;

        $this->actingAs($this->admin())
            ->put("/admin/paradis-deco/inspirations/{$inspiration->id}", [
                'title' => 'Salon', 'is_active' => 1, 'image' => $this->image(),
            ])->assertRedirect();

        $inspiration->refresh();

        $this->assertNotSame($old, $inspiration->image);
        $this->assertFalse(Storage::disk('public')->exists($old), 'Ancienne image non supprimée.');
        $this->assertTrue(Storage::disk('public')->exists($inspiration->image));
    }

    public function test_activation_et_desactivation(): void
    {
        $inspiration = $this->inspiration(['is_active' => false]);

        // Le contrôleur répond en JSON : l'interface admin est en AJAX.
        $this->actingAs($this->admin())
            ->putJson("/admin/paradis-deco/inspirations/toggle/{$inspiration->id}")
            ->assertOk();

        $this->assertTrue($inspiration->refresh()->is_active);
    }

    /** Une inspiration désactivée n'est pas accessible publiquement. */
    public function test_une_inspiration_desactivee_est_invisible(): void
    {
        $inspiration = $this->inspiration(['is_active' => false]);

        $this->get("/inspiration/{$inspiration->slug}")->assertNotFound();
        $this->get('/toutes/inspirations')->assertOk()->assertDontSee('Salon cosy', false);
    }

    public function test_suppression(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/paradis-deco/inspirations', [
                'title' => 'À supprimer', 'is_active' => 1, 'image' => $this->image(),
            ]);

        $inspiration = Inspiration::firstOrFail();
        $image = $inspiration->image;

        $this->actingAs($this->admin())
            ->deleteJson("/admin/paradis-deco/inspirations/{$inspiration->id}")
            ->assertOk();

        $this->assertSame(0, Inspiration::count());
        $this->assertFalse(Storage::disk('public')->exists($image), 'Image orpheline laissée sur le disque.');
    }

    /** Le front ne rend aucun vecteur, même pour du contenu déjà en base. */
    public function test_le_front_neutralise_le_contenu_herite(): void
    {
        $inspiration = $this->inspiration(['description' => self::XSS, 'resume' => '<img src=x onerror=alert(1)>R']);

        $html = $this->get("/inspiration/{$inspiration->slug}")->assertOk()->getContent();
        $body = Str::after($html, '</head>');

        foreach (['<script>alert', '<iframe', 'onclick="alert'] as $vector) {
            $this->assertStringNotContainsString($vector, $body, "Vecteur : {$vector}");
        }

        // Le résumé s'affiche en texte échappé, jamais interprété.
        $this->assertStringNotContainsString('<img src=x onerror', $body);
        $this->assertStringContainsString('<h2>Ambiance</h2>', $body);
    }

    public function test_le_seo_de_la_page_inspiration(): void
    {
        $inspiration = $this->inspiration();

        $html = $this->get("/inspiration/{$inspiration->slug}")->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html));
        $this->assertSame(1, preg_match_all('#<link rel="canonical"#', $html));
        $this->assertSame(1, preg_match_all('#<meta name="description"#', $html));
    }
}
