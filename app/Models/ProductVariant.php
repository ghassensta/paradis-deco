<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'type',
        'size',
        'price',
        'stock',
        'image',
        'position',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'stock' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /** Libellé affiché au panier, sur la commande et la facture. */
    public function getLabelAttribute(): string
    {
        return collect([$this->type, $this->size])->filter()->implode(' / ');
    }

    public function getImageUrlAttribute(): ?string
    {
        return Product::mediaUrl($this->image);
    }
}
