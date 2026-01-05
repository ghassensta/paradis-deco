<?php

namespace App\Models;

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
        'gallery',
        'is_active',
    ];

    protected $casts = [
        'slug' => 'string',
        'is_active' => 'boolean',
        'gallery' => 'array',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
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
