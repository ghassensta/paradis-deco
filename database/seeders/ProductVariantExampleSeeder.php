<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Support\VariantSync;
use GdImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Produits d'exemple avec types et tailles. Volontairement absent de
 * DatabaseSeeder : prix et stocks sont fictifs, à lancer à la demande avec
 * php artisan db:seed --class=ProductVariantExampleSeeder
 *
 * Les images sont des copies recadrées des photos du catalogue : supprimer un
 * produit efface ses fichiers, ils ne doivent donc pas être partagés.
 */
class ProductVariantExampleSeeder extends Seeder
{
    /** Chaque photo montre les deux tailles côte à côte : [x, y, largeur, hauteur] en fractions. */
    private const CROPS = [
        'Petite' => [0.0, 0.2, 0.5, 0.7],
        'Grande' => [0.5, 0.2, 0.5, 0.7],
    ];

    private const LAMP_PHOTOS = [
        'Naturel' => 'products/cover/7d7e0778-57a8-40a6-9ca7-40d59d160be6.webp',
        'Blanc' => 'products/cover/bb354633-b8b2-407e-98f3-5ec600cc6c79.webp',
        'Doré' => 'products/cover/efff8f39-5f0e-4ce9-98a8-c20f6f551907.webp',
        'Crème' => 'products/cover/0ab346cf-6e79-4c4f-8633-394af71e507a.webp',
        'Noir' => 'products/cover/8efaed27-642c-4e2e-abf9-6318ff838b6c.webp',
    ];

    private const CURTAIN_PHOTO = 'products/cover/f32d4c8b-1855-45b5-a7fe-396467f1fce7.webp';

    public function run(): void
    {
        $category = Category::where('slug', 'luminaires')->first();
        $categoryIds = $category ? [$category->id] : [];

        // Type + taille : l'image change avec les deux.
        $this->seedProduct($categoryIds, [
            'name' => 'Lampe de Table Design',
            'sku' => 'EXEMPLE-LAMPE-DESIGN',
            'description' => '<p>Lampe de table disponible en cinq finitions et deux tailles.</p>',
            'cover' => self::LAMP_PHOTOS['Doré'],
        ], collect(self::LAMP_PHOTOS)->flatMap(fn ($photo, $type) => [
            ['type' => $type, 'size' => 'Petite', 'price' => 89, 'stock' => 8, 'photo' => $photo, 'crop' => 'Petite'],
            // Doré en grande taille reste épuisé pour montrer l'option barrée.
            ['type' => $type, 'size' => 'Grande', 'price' => 129, 'stock' => $type === 'Doré' ? 0 : 5, 'photo' => $photo, 'crop' => 'Grande'],
        ])->all());

        // Taille seule, avec une image par taille.
        $this->seedProduct($categoryIds, [
            'name' => 'Lampe Noire Cannelée',
            'sku' => 'EXEMPLE-LAMPE-NOIRE',
            'description' => '<p>Lampe de table noire à pied cannelé, en deux tailles.</p>',
            'cover' => self::LAMP_PHOTOS['Noir'],
        ], [
            ['type' => null, 'size' => 'Petite', 'price' => 95, 'stock' => 6, 'photo' => self::LAMP_PHOTOS['Noir'], 'crop' => 'Petite'],
            ['type' => null, 'size' => 'Grande', 'price' => 139, 'stock' => 4, 'photo' => self::LAMP_PHOTOS['Noir'], 'crop' => 'Grande'],
        ]);

        // Taille seule, une image commune : seuls le prix et le stock changent.
        $this->seedProduct($categoryIds, [
            'name' => 'Rideau Lumineux Étoiles et Lunes',
            'sku' => 'EXEMPLE-RIDEAU-LUMINEUX',
            'description' => '<p>Rideau lumineux à motifs d\'étoiles et de lunes, en trois longueurs.</p>',
            'cover' => self::CURTAIN_PHOTO,
        ], [
            ['type' => null, 'size' => '2 m', 'price' => 39, 'stock' => 15],
            ['type' => null, 'size' => '3 m', 'price' => 55, 'stock' => 10],
            ['type' => null, 'size' => '4 m', 'price' => 69, 'stock' => 7],
        ]);
    }

    private function seedProduct(array $categoryIds, array $data, array $variants): void
    {
        $slug = Str::slug($data['name']);

        $product = Product::updateOrCreate(['slug' => $slug], [
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'],
            'image_avant' => $this->copyImage($data['cover'], "products/cover/exemple-{$slug}.webp"),
            'images' => [],
            'category_ids' => $categoryIds,
            'price' => 0,
            'stock' => 0,
            'is_active' => true,
        ]);

        foreach ($variants as $position => $variant) {
            $key = Str::slug(trim($variant['type'] . ' ' . $variant['size']));

            $product->variants()->updateOrCreate(
                ['type' => $variant['type'], 'size' => $variant['size']],
                [
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'position' => $position,
                    'is_active' => true,
                    'image' => isset($variant['photo'])
                        ? $this->copyImage($variant['photo'], "products/variants/exemple-{$slug}-{$key}.webp", self::CROPS[$variant['crop']])
                        : null,
                ]
            );
        }

        VariantSync::alignProduct($product);
    }

    /** Retourne null si la photo source est absente : la fiche retombe sur son visuel par défaut. */
    private function copyImage(string $source, string $target, ?array $crop = null): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($source)) {
            return null;
        }

        $image = imagecreatefromstring($disk->get($source));

        if ($crop) {
            [$x, $y, $width, $height] = $crop;

            $image = imagecrop($image, [
                'x' => (int) (imagesx($image) * $x),
                'y' => (int) (imagesy($image) * $y),
                'width' => (int) (imagesx($image) * $width),
                'height' => (int) (imagesy($image) * $height),
            ]);
        }

        $disk->put($target, $this->encode(imagescale($image, min(imagesx($image), 900))));

        return $target;
    }

    private function encode(GdImage $image): string
    {
        ob_start();
        imagewebp($image, null, 82);

        return ob_get_clean();
    }
}
