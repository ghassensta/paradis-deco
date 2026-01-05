<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:3',
        'subtotal' => 'decimal:3',
    ];

    /**
     * La commande à laquelle cet item appartient
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Le produit associé à cet item
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Boot method pour calculer automatiquement le subtotal et mettre à jour les totaux de la commande
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            if ($item->order) {
                $item->order->calculateTotals();
            }
        });
    }

    /**
     * Accessor pour le subtotal formaté
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 3, ',', ' ') . ' DT';
    }
}



