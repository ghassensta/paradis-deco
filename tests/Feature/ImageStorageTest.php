<?php

namespace Tests\Feature;

use App\Support\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Les uploads restent dans le disque de test : la suite n'écrit jamais
        // dans storage/app/public, où vivent les vraies images du site.
        Storage::fake('public');
    }

    /** Crée un vrai fichier image sur disque, pas un faux. */
    private function image(int $width, int $height, string $format = 'jpg'): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        imagefilledrectangle($gd, 0, 0, $width, $height, imagecolorallocate($gd, 200, 170, 130));

        $path = sys_get_temp_dir() . '/img-' . uniqid() . '.' . $format;

        match ($format) {
            'png' => imagepng($gd, $path),
            'webp' => imagewebp($gd, $path),
            default => imagejpeg($gd, $path, 90),
        };

        imagedestroy($gd);

        return new UploadedFile($path, "photo.{$format}", null, null, true);
    }

    /** @return array{0: int, 1: int, 2: string, 3: int} largeur, hauteur, mime, octets */
    private function inspect(string $path): array
    {
        $absolute = Storage::disk('public')->path($path);
        [$width, $height] = getimagesize($absolute);

        return [$width, $height, mime_content_type($absolute), filesize($absolute)];
    }

    public function test_un_jpg_devient_un_webp(): void
    {
        $path = ImageStorage::store($this->image(900, 600), 'inspirations', 'Lampe suspendue bois');

        [, , $mime] = $this->inspect($path);

        $this->assertStringEndsWith('.webp', $path);
        $this->assertSame('image/webp', $mime);
        $this->assertStringStartsWith('inspirations/lampe-suspendue-bois-', $path);
    }

    public function test_un_png_devient_un_webp(): void
    {
        $path = ImageStorage::store($this->image(900, 600, 'png'), 'categories');

        $this->assertSame('image/webp', $this->inspect($path)[2]);
    }

    public function test_un_webp_reste_un_webp(): void
    {
        $path = ImageStorage::store($this->image(900, 600, 'webp'), 'categories');

        $this->assertSame('image/webp', $this->inspect($path)[2]);
    }

    /** Une image trop grande est réduite, sans déformation. */
    public function test_une_grande_image_est_redimensionnee_sans_deformation(): void
    {
        $path = ImageStorage::store($this->image(4000, 2600), 'inspirations');

        [$width, $height] = $this->inspect($path);

        $this->assertSame(1600, $width, 'La largeur doit être plafonnée.');
        $this->assertEqualsWithDelta(4000 / 2600, $width / $height, 0.01, 'Le ratio doit être conservé.');
    }

    /** Une petite image n'est jamais agrandie. */
    public function test_une_petite_image_nest_pas_agrandie(): void
    {
        $path = ImageStorage::store($this->image(400, 300), 'inspirations');

        [$width, $height] = $this->inspect($path);

        $this->assertSame(400, $width);
        $this->assertSame(300, $height);
    }

    /** Chaque usage a sa propre largeur maximale. */
    public function test_la_largeur_maximale_depend_du_dossier(): void
    {
        $cover = ImageStorage::store($this->image(3000, 3000), 'products/cover');

        $this->assertSame(1200, $this->inspect($cover)[0]);
    }

    /** Le nom est généré côté serveur : le nom d'origine n'est jamais repris. */
    public function test_le_nom_de_fichier_est_genere_par_le_serveur(): void
    {
        $file = new UploadedFile($this->image(600, 400)->getRealPath(), '../../evil NAME.jpg', null, null, true);

        $path = ImageStorage::store($file, 'inspirations', 'Vase céramique blanc');

        $this->assertStringNotContainsString('..', $path);
        $this->assertStringNotContainsString(' ', $path);
        $this->assertMatchesRegularExpression('#^inspirations/vase-ceramique-blanc-[a-z0-9]{8}\.webp$#', $path);
    }

    /** Deux envois successifs ne se écrasent pas. */
    public function test_deux_envois_ne_se_chevauchent_pas(): void
    {
        $first = ImageStorage::store($this->image(600, 400), 'inspirations', 'Meme titre');
        $second = ImageStorage::store($this->image(600, 400), 'inspirations', 'Meme titre');

        $this->assertNotSame($first, $second);
        $this->assertTrue(Storage::disk('public')->exists($first));
        $this->assertTrue(Storage::disk('public')->exists($second));
    }

    public function test_le_remplacement_supprime_lancienne_image(): void
    {
        $old = ImageStorage::store($this->image(600, 400), 'inspirations');
        $new = ImageStorage::replace($this->image(600, 400), 'inspirations', $old);

        $this->assertFalse(Storage::disk('public')->exists($old), 'Pas de fichier orphelin.');
        $this->assertTrue(Storage::disk('public')->exists($new));
    }

    /** Une URL absolue (import, CDN) ne doit jamais être supprimée du disque. */
    public function test_une_url_absolue_nest_pas_supprimee(): void
    {
        ImageStorage::delete('https://exemple.tn/photo.jpg');

        $this->assertTrue(true, 'Aucune exception ne doit être levée.');
    }

    /** La règle refuse les formats dangereux et accepte les formats matriciels. */
    public function test_la_regle_de_validation_est_restrictive(): void
    {
        $rule = ImageStorage::rule();

        $this->assertStringContainsString('image', $rule);
        $this->assertStringContainsString('mimes:jpg,jpeg,png,webp', $rule);
        $this->assertStringContainsString('max:' . ImageStorage::MAX_KILOBYTES, $rule);
        $this->assertStringContainsString('dimensions:min_width=200', $rule);
        $this->assertStringNotContainsString('svg', $rule);

        $this->assertStringStartsWith('required|', ImageStorage::rule(required: true));
        $this->assertStringStartsWith('nullable|', $rule);
    }

    /* ---------------- Déclinaisons réduites (vignettes) ---------------- */

    public function test_une_declinaison_reduite_est_generee_a_cote_de_l_original(): void
    {
        // 1200 est la largeur maximale du dossier « categories » : l'original
        // reste donc bien plus large que la vignette demandée.
        $original = ImageStorage::store($this->image(2400, 2400), 'categories', 'Salon');

        $variante = ImageStorage::variant($original, 480);

        $this->assertNotNull($variante);
        $this->assertSame(str_replace('.webp', '-480.webp', $original), $variante);

        [$l, $h, $mime, $poids] = $this->inspect($variante);
        $this->assertSame(480, $l);
        $this->assertSame(480, $h);
        $this->assertSame('image/webp', $mime);

        // L'original n'est ni remplacé ni modifié : il sert ailleurs.
        $this->assertTrue(Storage::disk('public')->exists($original));
        [$lo] = $this->inspect($original);
        $this->assertSame(1200, $lo);
        $this->assertLessThan($this->inspect($original)[3], $poids);
    }

    public function test_une_image_deja_assez_petite_n_est_pas_re_encodee(): void
    {
        $original = ImageStorage::store($this->image(360, 360), 'categories', 'Petite');

        $this->assertNull(ImageStorage::variant($original, 480));
        $this->assertFalse(Storage::disk('public')->exists(str_replace('.webp', '-480.webp', $original)));
    }

    public function test_une_declinaison_existante_est_reutilisee_sans_regeneration(): void
    {
        $original = ImageStorage::store($this->image(2400, 1600), 'categories', 'Reutilisee');
        $variante = ImageStorage::variant($original, 480);

        $empreinte = md5(Storage::disk('public')->get($variante));

        // Nouvel appel dans un autre contexte que le cache mémoire statique.
        $this->assertSame($variante, ImageStorage::variant($original, 480));
        $this->assertSame($empreinte, md5(Storage::disk('public')->get($variante)));
    }

    public function test_variant_url_laisse_passer_ce_qui_n_est_pas_sur_le_disque_public(): void
    {
        // Placeholder local servi hors du disque public.
        $this->assertSame(
            asset('images/no-image.png'),
            ImageStorage::variantUrl(asset('images/no-image.png'), 480)
        );

        // Fichier absent : la page retombe sur la valeur d'origine.
        $this->assertSame(
            'categories/inexistante.webp',
            ImageStorage::variantUrl('categories/inexistante.webp', 480)
        );

        $this->assertSame('', ImageStorage::variantUrl(null, 480));
    }

    public function test_variant_url_accepte_une_url_complete_et_renvoie_la_vignette(): void
    {
        $original = ImageStorage::store($this->image(2000, 2000), 'categories', 'Depuis url');

        $url = ImageStorage::variantUrl(asset('storage/' . $original), 480);

        $this->assertStringEndsWith('-480.webp', $url);
        $this->assertTrue(Storage::disk('public')->exists(str_replace('.webp', '-480.webp', $original)));
    }

    public function test_un_fichier_illisible_ne_leve_pas_d_exception(): void
    {
        Storage::disk('public')->put('categories/corrompue.webp', 'ceci n\'est pas une image');

        $this->assertNull(ImageStorage::variant('categories/corrompue.webp', 480));
    }
}
