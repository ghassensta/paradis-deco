<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    protected $fillable = [
        'category_ids',
        'image_avant',
        'images',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
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
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
