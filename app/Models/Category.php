<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFaqs;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasFaqs;

    protected $fillable = [
        'parent_id',
        'image',
        'name',
        'slug',
        'description',
        'description_courte',
        'is_active',
        'sort_order',

        // SEO
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* =======================
     | Relations
     ======================= */

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function activeChildren()
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Les produits sont rattachés via la colonne JSON products.category_ids :
     * ce n'est pas une clé étrangère, d'où la requête explicite.
     */
    public function products()
    {
        return Product::query()
            ->where(fn ($q) => $q->whereJsonContains('category_ids', $this->id)
                ->orWhereJsonContains('category_ids', (string) $this->id));
    }

    /* =======================
     | Scopes
     ======================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /* =======================
     | Hiérarchie
     ======================= */

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /** Catégorie racine puis descendants jusqu'à $this. */
    public function ancestorsAndSelf(): array
    {
        return $this->parent ? [$this->parent, $this] : [$this];
    }

    /**
     * Empêche qu'une catégorie devienne son propre parent ou descendant.
     * La hiérarchie est volontairement limitée à deux niveaux.
     */
    public function canHaveParent(?int $parentId): bool
    {
        if ($parentId === null) {
            return true;
        }

        if ($this->exists && $parentId === $this->id) {
            return false;
        }

        $parent = self::find($parentId);

        if (! $parent) {
            return false;
        }

        // Le parent doit être une racine, et cette catégorie ne doit pas
        // déjà avoir des enfants (sinon on créerait un troisième niveau).
        if (! $parent->isRoot()) {
            return false;
        }

        return ! ($this->exists && $this->children()->exists());
    }

    /* =======================
     | Model Events
     ======================= */

    protected static function boot()
    {
        parent::boot();

        $prepare = function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::uniqueSlug($category->name, $category->id);
            }

            $category->meta_title ??= $category->name;
        };

        static::creating($prepare);
        static::updating($prepare);
    }

    /** Garantit l'unicité du slug sans écraser celui d'une autre catégorie. */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'categorie';
        $slug = $base;
        $i = 2;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /* =======================
     | Accessors / SEO
     ======================= */

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }

    /** Titre SEO : personnalisé si saisi, sinon fallback distinct par niveau. */
    public function getSeoTitleAttribute()
    {
        if ($this->meta_title && $this->meta_title !== $this->name) {
            return $this->meta_title;
        }

        $site = config('app.name');

        return $this->isRoot()
            ? "{$this->name} | Décoration d'intérieur — {$site}"
            : "{$this->name} — {$this->parent?->name} | {$site}";
    }

    public function getSeoDescriptionAttribute()
    {
        if ($this->meta_description) {
            return Str::limit(RichText::toText($this->meta_description), 160);
        }

        if ($this->description_courte) {
            return Str::limit(RichText::toText($this->description_courte), 160);
        }

        if ($this->description) {
            return Str::limit(RichText::toText($this->description), 160);
        }

        return $this->isRoot()
            ? "Découvrez notre sélection {$this->name} : pièces déco choisies pour sublimer votre intérieur."
            : "{$this->name} — notre sélection {$this->parent?->name} pour sublimer votre intérieur.";
    }

    /** Résumé affiché en tête de page, avec repli sur la description longue. */
    public function getShortDescriptionAttribute(): string
    {
        if (filled($this->description_courte)) {
            return trim($this->description_courte);
        }

        return Str::limit(RichText::toText($this->description), 300);
    }

    /** HTML éditorial re-nettoyé à l'affichage (défense en profondeur). */
    public function getSafeDescriptionAttribute(): string
    {
        return RichText::toHtml($this->description);
    }

    public function getEditorDescriptionAttribute(): string
    {
        return $this->safe_description;
    }

    /** Évite d'afficher deux fois le même texte quand le repli suffit. */
    public function hasDistinctLongDescription(): bool
    {
        if (blank($this->description)) {
            return false;
        }

        $normalize = fn (?string $v) => Str::lower(RichText::toText($v));

        return $normalize($this->description) !== $normalize($this->short_description);
    }

    /** URL canonique : /collections/parent/enfant pour une sous-catégorie. */
    public function getUrlAttribute(): string
    {
        return $this->isRoot()
            ? route('categorie.produits', $this->slug)
            : route('categorie.sous-categorie', [$this->parent->slug, $this->slug]);
    }
}
