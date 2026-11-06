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

        // Contact
        'support_phone',
        'address',
        'city',
        'working_hours',

        // Réseaux sociaux
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'whatsapp_number',

        // SEO
        'og_image',

        // Tracking
        'meta_pixel_id',
        'meta_pixel_enabled',
        'tiktok_pixel_id',
        'tiktok_pixel_enabled',
        'ga4_measurement_id',
        'ga4_enabled',
    ];


     protected $casts = [
        'shipping_cost'            => 'float',
        'free_shipping_threshold'  => 'float',
        'delivery_estimate_days'   => 'integer',
        'maintenance_mode'         => 'boolean',
        'meta_pixel_enabled'       => 'boolean',
        'tiktok_pixel_enabled'     => 'boolean',
        'ga4_enabled'              => 'boolean',
    ];
}
