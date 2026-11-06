<?php

namespace App\Models;

use App\Models\Concerns\HasFaqs;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFaqs;

    protected $table = "products";

    protected $fillable = [
        'category_ids',
        'image_avant',
        'images',
        'name',
        'slug',
        'description',
        'description_courte',
        'price',
        'stock',
        'sku',
        'is_active',
        'has_variants',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    protected $casts = [
        'images' => 'array',
        'category_ids' => 'array',
        'price' => 'decimal:3',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * D'anciennes écritures ont stocké ces colonnes en JSON doublement encodé
     * (json_encode() appliqué en plus du cast 'array'). Le cast rend alors une
     * chaîne et les vues cassent sur foreach. On redécode le cas échéant.
     */
    protected function decodeJsonList(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? array_values($value) : [];
    }

    public function getImagesAttribute($value): array
    {
        return $this->decodeJsonList($this->castAttribute('images', $value));
    }

    public function getCategoryIdsAttribute($value): array
    {
        return $this->decodeJsonList($this->castAttribute('category_ids', $value));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Catégorie la plus spécifique du produit : une sous-catégorie si le produit
     * y est rattaché, sinon la catégorie principale. Sert au fil d'Ariane.
     */
    /**
     * URL d'un média : les valeurs déjà absolues (CDN, import) sont conservées,
     * les chemins relatifs passent par le disque public.
     */
    /** Titre SEO : personnalisé si saisi, sinon fallback unique par produit. */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->name . ' | ' . config('app.name');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return Str::limit(
            RichText::toText(
                $this->meta_description
                    ?: $this->description_courte
                    ?: $this->description
                    ?: $this->name
            ),
            160
        );
    }

    /**
     * Résumé affiché près du prix. Les fiches créées avant l'ajout du champ
     * n'en ont pas : on retombe alors sur les premières phrases de la
     * description longue plutôt que d'afficher une zone vide.
     */
    public function getShortDescriptionAttribute(): string
    {
        if (filled($this->description_courte)) {
            return trim($this->description_courte);
        }

        return Str::limit(RichText::toText($this->description), 300);
    }

    /**
     * HTML de la description longue, re-nettoyé à l'affichage. Les lignes
     * enregistrées avant la mise en place du filtre serveur sont ainsi
     * couvertes elles aussi, et le texte brut hérité devient des paragraphes.
     */
    public function getSafeDescriptionAttribute(): string
    {
        return RichText::toHtml($this->description);
    }

    /** Contenu prêt pour l'éditeur Quill (même normalisation qu'à l'affichage). */
    public function getEditorDescriptionAttribute(): string
    {
        return $this->safe_description;
    }

    /**
     * Vrai lorsque la description longue apporte réellement du contenu
     * supplémentaire : évite d'afficher deux fois le même texte.
     */
    public function hasDistinctLongDescription(): bool
    {
        if (blank($this->description)) {
            return false;
        }

        $normalize = fn (?string $v) => Str::lower(RichText::toText($v));

        return $normalize($this->description) !== $normalize($this->short_description);
    }

    public static function mediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '//'])
            ? $path
            : Storage::url($path);
    }

    /** Visuel principal, avec repli sur le placeholder local versionné. */
    public function getCoverUrlAttribute(): string
    {
        return self::mediaUrl($this->image_avant)
            ?? self::mediaUrl($this->images[0] ?? null)
            ?? asset('images/no-image.png');
    }

    public function primaryCategory(): ?Category
    {
        $ids = $this->category_ids;

        if (empty($ids)) {
            return null;
        }

        // Le parent est préchargé : fil d'Ariane et section description
        // l'utilisent tous deux sans requête additionnelle.
        $categories = Category::whereIn('id', $ids)
            ->where('is_active', true)
            ->with('parent')
            ->get();

        return $categories->firstWhere('parent_id', '!=', null) ?? $categories->first();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->ordered();
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->active();
    }

    /** Variantes vendables, au format attendu par le sélecteur de la fiche. */
    public function storefrontVariants(): array
    {
        if (! $this->has_variants) {
            return [];
        }

        return $this->activeVariants->map(fn (ProductVariant $variant) => [
            'id' => $variant->id,
            'type' => $variant->type,
            'size' => $variant->size,
            'label' => $variant->label,
            'price' => (float) $variant->price,
            'stock' => $variant->stock,
            'image' => $variant->image_url ?? $this->cover_url,
        ])->all();
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'product_id', 'id');
    }

    /**
     * Obtenir la note moyenne des avis approuvés.
     */
    public function getAverageRatingAttribute()
    {
        $approvedReviews = $this->avis()->where('approved', true)->get();

        if ($approvedReviews->isEmpty()) {
            return 5.0; // Note par défaut
        }

        return round($approvedReviews->avg('rating'), 1);
    }

    /**
     * Obtenir le nombre total d'avis approuvés.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->avis()->where('approved', true)->count();
    }

    /**
     * Vérifier si le produit a des avis.
     */
    public function hasReviews()
    {
        return $this->total_reviews > 0;
    }
}
