<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /* devises autorisées */
    'currency'                => 'required|string|in:USD,EUR,GBP',

    'meta_title'              => 'nullable|string|max:255',
    'meta_description'        => 'nullable|string|max:1000',

    'shipping_cost'           => 'required|numeric|min:0',
    'free_shipping_threshold' => 'required|numeric|min:0',
    'delivery_estimate_days'  => 'required|integer|min:1',
    'maintenance_mode'        => 'required|boolean',

    /* fichiers : JPEG / PNG / JPG / WEBP / AVIF / GIF */
    'site_logo'       => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:2048',
    'homepage_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:2048',
]);


    $setting = Configuration::firstOrFail();

    /* 2. Gestion du logo */
    if ($request->hasFile('site_logo')) {

        // Supprimer l’ancien fichier s’il existe physiquement
        if ($setting->site_logo && file_exists(public_path($setting->site_logo))) {
            @unlink(public_path($setting->site_logo));
        }

        // Nouveau nom de fichier (slug + id unique)
        $filename = Str::slug($request->site_name).'-logo-'.uniqid().'.'.$request->site_logo->extension();

        // Dossier de destination dans /public
        $request->site_logo->move(public_path('storage/logos'), $filename);

        // Stocke le chemin relatif (pour asset())
        $validated['site_logo'] = 'logos/'.$filename;
    }

    /* 3. Gestion du banner */
    if ($request->hasFile('homepage_banner')) {

        if ($setting->homepage_banner && file_exists(public_path($setting->homepage_banner))) {
            @unlink(public_path($setting->homepage_banner));
        }

        $filename = 'banner-'.uniqid().'.'.$request->homepage_banner->extension();
        $request->homepage_banner->move(public_path('storage/banners'), $filename);
        $validated['homepage_banner'] = 'banners/'.$filename;
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
