<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = "orders";

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
        'notes',
    ];

    protected $casts = [
        'subtotal_ht' => 'decimal:3',
        'shipping_cost' => 'decimal:3',
        'tax_rate' => 'decimal:2',
        'tax_tva' => 'decimal:3',
        'total_ttc' => 'decimal:3',
        'shipped_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Boot method pour générer automatiquement le numéro de commande
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->numero_commande)) {
                $order->numero_commande = 'CMD-' . date('Ymd') . '-' . str_pad(static::max('id') + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Le client qui a passé la commande
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Le statut actuel de la commande
     */
    public function statut()
    {
        return $this->belongsTo(Statut::class, 'statut_id');
    }

    /**
     * Les articles rattachés à cette commande
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $statusId)
    {
        return $query->where('statut_id', $statusId);
    }

    /**
     * Scope pour les commandes récentes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor pour le total formaté
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_ttc, 3, ',', ' ') . ' DT';
    }

    /**
     * Méthode pour calculer et mettre à jour les totaux de la commande
     */
    public function calculateTotals()
    {
        $subtotal_ht = $this->items()->sum('subtotal');
        $tax_tva = $subtotal_ht * ($this->tax_rate / 100);
        $total_ttc = $subtotal_ht + $this->shipping_cost + $tax_tva;

        $this->subtotal_ht = $subtotal_ht;
        $this->tax_tva = $tax_tva;
        $this->total_ttc = $total_ttc;

        // Mise à jour silencieuse pour éviter les boucles infinies
        $this->saveQuietly();
    }
}
