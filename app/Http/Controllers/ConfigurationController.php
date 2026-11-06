<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Configuration::first();
        return view('admin.configuration.index', ["settings" => $settings]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        /* 1. Validation */
        $validated = $request->validate([
            'site_name'               => 'required|string|max:255',
            'support_email'           => 'required|email|max:255',
            /* langues dispos */
            'default_language'        => 'required|string|in:en,fr,es',
            /* devises autorisées — TND ajouté : la boutique vend en Tunisie */
            'currency'                => 'required|string|in:TND,USD,EUR,GBP',
            'meta_title'              => 'nullable|string|max:255',
            'meta_description'        => 'nullable|string|max:1000',
            'shipping_cost'           => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'delivery_estimate_days'  => 'required|integer|min:1',
            'maintenance_mode'        => 'required|boolean',
            /* fichiers : JPEG / PNG / JPG / WEBP / AVIF / GIF */
            'site_logo'       => ImageStorage::rule(),
            'homepage_banner' => ImageStorage::rule(),
            'og_image'        => ImageStorage::rule(),

            /* Contact — alimentent le pied de page et les données structurées */
            'support_phone'   => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:120',
            'working_hours'   => 'nullable|string|max:255',

            /* Réseaux sociaux */
            'facebook_url'    => 'nullable|url|max:255',
            'instagram_url'   => 'nullable|url|max:255',
            'tiktok_url'      => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:30',

            /* Tracking — l'identifiant reste libre : les formats évoluent */
            'meta_pixel_id'       => 'nullable|string|max:64',
            'meta_pixel_enabled'  => 'nullable|boolean',
            'tiktok_pixel_id'     => 'nullable|string|max:64',
            'tiktok_pixel_enabled' => 'nullable|boolean',
            'ga4_measurement_id'  => 'nullable|string|max:64',
            'ga4_enabled'         => 'nullable|boolean',
        ]);

        // Les cases décochées ne sont pas postées : on les force à false.
        foreach (['meta_pixel_enabled', 'tiktok_pixel_enabled', 'ga4_enabled'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }

        $setting = Configuration::firstOrFail();

        /* 2. Gestion du logo */
        if ($request->hasFile('site_logo')) {
            $validated['site_logo'] = ImageStorage::replace(
                $request->file('site_logo'),
                'logos',
                $setting->site_logo
            );
        }

        /* 3. Gestion du banner */
        if ($request->hasFile('homepage_banner')) {
            $validated['homepage_banner'] = ImageStorage::replace(
                $request->file('homepage_banner'),
                'banners',
                $setting->homepage_banner
            );
        }

        /* 3 bis. Image Open Graph */
        if ($request->hasFile('og_image')) {
            $validated['og_image'] = ImageStorage::replace(
                $request->file('og_image'),
                'banners',
                $setting->og_image
            );
        }

        /* 4. Mise à jour */
        $setting->update($validated);

        return back()->with('message', 'Paramètres mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
