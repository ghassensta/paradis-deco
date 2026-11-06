<?php

namespace App\Support;

use App\Models\Product;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Synchronise les variantes envoyées par le formulaire produit avec la base,
 * puis aligne le prix et le stock de la fiche sur les variantes actives.
 */
final class VariantSync
{
    private const IMAGE_DIRECTORY = 'products/variants';

    /** Règles de validation à fusionner dans celles du formulaire produit. */
    public static function rules(string $key = 'variants'): array
    {
        return [
            $key => ['nullable', 'array', 'max:30', self::consistencyRule()],
            $key . '.*.id' => 'nullable|integer',
            $key . '.*.type' => 'nullable|string|max:100',
            $key . '.*.size' => 'nullable|string|max:100',
            $key . '.*.price' => 'nullable|numeric|min:0',
            $key . '.*.stock' => 'nullable|integer|min:0',
            $key . '.*.image' => ImageStorage::rule(),
            $key . '.*.position' => 'nullable|integer|min:0|max:9999',
            $key . '.*.is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Le sélecteur de la fiche produit suppose des lignes homogènes : toutes
     * renseignent les mêmes champs, et une combinaison n'existe qu'une fois.
     */
    private static function consistencyRule(): Closure
    {
        return function (string $attribute, mixed $rows, Closure $fail): void {
            $filled = collect(is_array($rows) ? $rows : [])
                ->map(fn ($row) => [self::clean($row['type'] ?? null), self::clean($row['size'] ?? null)])
                ->reject(fn ($row) => $row === [null, null]);

            $shapes = $filled->map(fn ($row) => [$row[0] !== null, $row[1] !== null])->unique();

            if ($shapes->count() > 1) {
                $fail('Toutes les variantes doivent renseigner les mêmes champs (type, taille, ou les deux).');

                return;
            }

            $keys = $filled->map(fn ($row) => Str::lower(implode('|', $row)));

            if ($keys->unique()->count() !== $keys->count()) {
                $fail('Chaque combinaison de type et de taille ne peut exister qu\'une seule fois.');
            }
        };
    }

    /** @param  array<int, array<string, mixed>>|null  $rows */
    public static function apply(Product $product, ?array $rows): void
    {
        $keptIds = [];
        $position = 0;

        foreach ($rows ?? [] as $row) {
            $type = self::clean($row['type'] ?? null);
            $size = self::clean($row['size'] ?? null);

            // Une ligne vide (ajoutée puis abandonnée) est simplement ignorée.
            if ($type === null && $size === null) {
                continue;
            }

            $attributes = [
                'type' => $type,
                'size' => $size,
                // Sans prix saisi, la variante reprend le prix de la fiche.
                'price' => filled($row['price'] ?? null) ? $row['price'] : $product->price,
                'stock' => (int) ($row['stock'] ?? 0),
                'position' => filled($row['position'] ?? null) ? (int) $row['position'] : $position,
                'is_active' => (bool) ($row['is_active'] ?? false),
            ];

            $existing = filled($row['id'] ?? null)
                ? $product->variants()->whereKey($row['id'])->first()
                : null;

            $file = $row['image'] ?? null;

            if ($file instanceof UploadedFile) {
                $attributes['image'] = ImageStorage::replace(
                    $file,
                    self::IMAGE_DIRECTORY,
                    $existing?->image,
                    $product->name
                );
            }

            $variant = $existing
                ? tap($existing)->update($attributes)
                : $product->variants()->create($attributes);

            $keptIds[] = $variant->id;
            $position++;
        }

        // Les lignes absentes du formulaire ont été supprimées par l'admin.
        $product->variants()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(function ($variant) {
                ImageStorage::delete($variant->image);
                $variant->delete();
            });

        self::alignProduct($product);
    }

    /**
     * Listes, tri, sitemap et données structurées lisent la fiche : elle porte
     * donc le prix « à partir de » et le stock cumulé des variantes actives.
     */
    public static function alignProduct(Product $product): void
    {
        $active = $product->variants()->active()->get();

        $product->update($active->isEmpty()
            ? ['has_variants' => false]
            : [
                'has_variants' => true,
                'price' => $active->min('price'),
                'stock' => $active->sum('stock'),
            ]);
    }

    private static function clean(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
