<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inspiration extends Model
{
    protected $table = 'inspirations';

    protected $fillable = [
        'image',
        'title',
        'slug',
        'resume',
        'description',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'slug' => 'string',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    /**
     * Contenu re-nettoyé à l'affichage : couvre aussi les inspirations
     * enregistrées avant la mise en place du filtre serveur.
     */
    public function getSafeDescriptionAttribute(): string
    {
        return RichText::toHtml($this->description);
    }

    /** Résumé toujours rendu en texte : il s'affiche dans des cartes. */
    public function getSafeResumeAttribute(): string
    {
        return RichText::toText($this->resume);
    }

    public function getEditorDescriptionAttribute(): string
    {
        return $this->safe_description;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope for active inspirations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate unique slug if not provided
        static::creating(function ($inspiration) {
            if (empty($inspiration->slug)) {
                $inspiration->slug = Str::slug($inspiration->title);
                $baseSlug = $inspiration->slug;
                $counter = 1;
                while (static::where('slug', $inspiration->slug)->exists()) {
                    $inspiration->slug = $baseSlug . '-' . $counter++;
                }
            }
        });

        static::updating(function ($inspiration) {
            if ($inspiration->isDirty('title') && empty($inspiration->slug)) {
                $inspiration->slug = Str::slug($inspiration->title);
                $baseSlug = $inspiration->slug;
                $counter = 1;
                while (static::where('slug', $inspiration->slug)->where('id', '!=', $inspiration->id)->exists()) {
                    $inspiration->slug = $baseSlug . '-' . $counter++;
                }
            }
        });
    }
}
