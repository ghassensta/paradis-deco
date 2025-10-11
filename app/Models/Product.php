<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";
    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'category_ids',
        'images',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        // Champs SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    /**
     * Casts des attributs.
     */
    protected $casts = [
        'images' => 'array',
        'category_ids' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Utiliser le slug pour le Route Model Binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope pour ne récupérer que les produits actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function avis(){

        return $this->hasMany(Avis::class,'product_id','id');
    }

}
