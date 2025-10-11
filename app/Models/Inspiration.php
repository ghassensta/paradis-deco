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
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate slug if not provided
        static::creating(function ($inspiration) {
            if (empty($inspiration->slug)) {
                $inspiration->slug = Str::slug($inspiration->title);
            }
        });

        static::updating(function ($inspiration) {
            if (empty($inspiration->slug)) {
                $inspiration->slug = Str::slug($inspiration->title);
            }
        });
    }
}
