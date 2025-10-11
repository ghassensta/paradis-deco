<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
     protected $fillable = [
        'site_name',
        'site_logo',
        'support_email',
        'default_language',
        'currency',
        'meta_title',
        'meta_description',
        'shipping_cost',
        'free_shipping_threshold',
        'delivery_estimate_days',
        'maintenance_mode',
        'homepage_banner',
    ];


     protected $casts = [
        'shipping_cost'            => 'float',
        'free_shipping_threshold'  => 'float',
        'delivery_estimate_days'   => 'integer',
        'maintenance_mode'         => 'boolean',
    ];
}
