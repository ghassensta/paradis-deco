<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'adresse',
    ];

    /**
     * The attributes that should be hidden for arrays (e.g. JSON output).
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function order()
    {
        return $this->hasMany(Order::class, 'client_id', 'id');
    }
}
