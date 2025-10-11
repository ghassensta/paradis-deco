<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    protected $table = "statuts";

    protected $fillable = [
        "name",
        "is_publish"
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, "statut_id", "id");
    }
}
