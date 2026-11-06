<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Point unique pour les images téléversées depuis l'administration.
 *
 * Chaque contrôleur dupliquait la même séquence (lire, convertir en WebP,
 * écrire sur le disque public) sans redimensionnement ni garde de taille :
 * une photo de 6000 × 4000 était servie telle quelle au navigateur.
 *
 * Séquence appliquée ici : validation → redimensionnement → WebP → stockage,
 * avec un nom de fichier généré côté serveur.
 */
final class ImageStorage
{
    /** Qualité WebP : compromis retenu pour le poids sans perte visible. */
    private const QUALITY = 82;

    /** Largeurs maximales par usage, adaptées au thème. */
    private const MAX_WIDTHS = [
        'inspirations' => 1600,
        'products/cover' => 1200,
        'products/media' => 1200,
        'products/variants' => 1200,
        'categories' => 1200,
        'banners' => 1920,
        'logos' => 600,
    ];

    private const DEFAULT_MAX_WIDTH = 1600;

    /** Poids maximal accepté, en kilo-octets. */
    public const MAX_KILOBYTES = 8192;

    /**
     * Règle de validation à réutiliser dans les contrôleurs.
     * `image` vérifie le contenu réel du fichier, pas seulement l'extension ;
     * `mimes` restreint aux formats matriciels sûrs (pas de SVG).
     */
    public static function rule(bool $required = false): string
    {
        return implode('|', array_filter([
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:' . self::MAX_KILOBYTES,
            'dimensions:min_width=200,min_height=200',
        ]));
    }

    /**
     * Convertit et stocke une image, puis retourne son chemin relatif au
     * disque public. Le nom est dérivé d'un libellé lisible (utile au
     * référencement) suivi d'un identifiant unique pour éviter les collisions.
     */
    public static function store(UploadedFile $file, string $directory, ?string $basename = null): string
    {
        $directory = trim($directory, '/');

        $image = Image::read($file->getRealPath());

        // Réduction uniquement : une petite image n'est jamais agrandie.
        $image->scaleDown(width: self::MAX_WIDTHS[$directory] ?? self::DEFAULT_MAX_WIDTH);

        $path = $directory . '/' . self::filename($basename);

        Storage::disk('public')->put($path, (string) $image->toWebp(self::QUALITY));

        return $path;
    }

    /**
     * Remplace une image : stocke la nouvelle puis supprime l'ancienne.
     * L'ancienne n'est retirée qu'une fois la nouvelle écrite, pour ne jamais
     * laisser une fiche sans visuel en cas d'échec.
     */
    public static function replace(UploadedFile $file, string $directory, ?string $previous, ?string $basename = null): string
    {
        $path = self::store($file, $directory, $basename);

        if ($previous && $previous !== $path) {
            self::delete($previous);
        }

        return $path;
    }

    /**
     * Supprime un fichier du disque public. Les valeurs absolues (images
     * importées depuis un CDN, par exemple) ne sont jamais touchées.
     */
    public static function delete(?string $path): void
    {
        if (blank($path) || Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /**
     * URL d'une variante réduite d'une image déjà stockée.
     *
     * Les visuels historiques ont été téléversés avant la mise en place du
     * redimensionnement : une photo 3024 × 4032 pouvait être servie dans une
     * vignette de 200 px. Cette méthode produit — une seule fois, puis réutilise
     * — une déclinaison au format « base-480.webp », en reprenant la convention
     * de nommage déjà employée pour la bannière d'accueil.
     *
     * `$source` accepte aussi bien un chemin disque qu'une URL complète : tout
     * ce qui ne pointe pas vers le disque public (placeholder local, CDN) est
     * renvoyé tel quel.
     */
    public static function variantUrl(?string $source, int $width): string
    {
        if (blank($source)) {
            return '';
        }

        $path = self::relativePath($source);

        if ($path === null) {
            return $source;
        }

        $variant = self::variant($path, $width);

        return $variant ? asset('storage/' . $variant) : $source;
    }

    /**
     * Chemin de la variante, générée à la demande. Retourne null lorsque
     * l'original est déjà assez petit ou qu'il ne peut pas être traité : la
     * page retombe alors sur l'image d'origine plutôt que de casser.
     */
    public static function variant(string $path, int $width): ?string
    {
        static $memo = [];

        $cle = $path . '@' . $width;

        if (array_key_exists($cle, $memo)) {
            return $memo[$cle];
        }

        $disque = Storage::disk('public');
        $cible = self::variantPath($path, $width);

        if ($disque->exists($cible)) {
            return $memo[$cle] = $cible;
        }

        if (! $disque->exists($path)) {
            return $memo[$cle] = null;
        }

        try {
            $image = Image::read($disque->path($path));

            // Déjà à la bonne échelle : inutile de ré-encoder.
            if ($image->width() <= $width) {
                return $memo[$cle] = null;
            }

            $disque->put($cible, (string) $image->scaleDown(width: $width)->toWebp(self::QUALITY));
        } catch (\Throwable $e) {
            return $memo[$cle] = null;
        }

        return $memo[$cle] = $cible;
    }

    /** « categories/photo.webp » + 480 => « categories/photo-480.webp ». */
    private static function variantPath(string $path, int $width): string
    {
        $dossier = trim(dirname($path), '/.');
        $nom = pathinfo($path, PATHINFO_FILENAME);

        return ($dossier !== '' ? $dossier . '/' : '') . $nom . '-' . $width . '.webp';
    }

    /** URL ou chemin => chemin relatif au disque public, ou null si externe. */
    private static function relativePath(string $source): ?string
    {
        if (Str::startsWith($source, ['http://', 'https://', '//'])) {
            $position = strpos($source, '/storage/');

            return $position === false ? null : substr($source, $position + strlen('/storage/'));
        }

        return ltrim($source, '/');
    }

    /** Nom lisible et unique : « lampe-suspendue-bois-a1b2c3d4.webp ». */
    private static function filename(?string $basename): string
    {
        $slug = Str::of((string) $basename)->slug()->limit(60, '')->value();

        return ($slug !== '' ? $slug . '-' : '') . Str::lower(Str::random(8)) . '.webp';
    }
}
