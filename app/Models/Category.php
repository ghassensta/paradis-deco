<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image',
        'name',
        'slug',
        'is_active',

        // SEO
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /* =======================
     | Relations
     ======================= */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /* =======================
     | Model Events
     ======================= */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // SEO fallback
            $category->meta_title ??= $category->name;
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // SEO fallback
            $category->meta_title ??= $category->name;
        });
    }

    /* =======================
     | Accessors
     ======================= */

    // Image URL
    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }

    // SEO title fallback
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?: $this->name;
    }

    // SEO description fallback
    public function getSeoDescriptionAttribute()
    {
        return $this->meta_description
            ?: Str::limit(strip_tags($this->name), 160);
    }
}
