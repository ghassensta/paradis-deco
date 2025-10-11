<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Statut;
use App\Models\OrderItem;

class Order extends Model
{
    protected $tables = "orders";
    protected $fillable = [
        'numero_commande',
        'client_id',
        'statut_id',
        'subtotal_ht',
        'shipping_cost',
        'tax_rate',
        'tax_tva',
        'total_ttc',
        'shipped_at',
        'paid_at',
    ];

    protected $dates = [
        'shipped_at',
        'paid_at',
        'deleted_at',
    ];

    // ----------------------
    // Relations
    // ----------------------

    /**
     * Le client qui a passé la commande.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Le statut actuel de la commande.
     */
    public function statut()
    {
        return $this->belongsTo(Statut::class, 'statut_id');
    }

    /**
     * Les articles rattachés à cette commande.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
