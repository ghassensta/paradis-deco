<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
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
            /* devises autorisées */
            'currency'                => 'required|string|in:USD,EUR,GBP',
            'meta_title'              => 'nullable|string|max:255',
            'meta_description'        => 'nullable|string|max:1000',
            'shipping_cost'           => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'delivery_estimate_days'  => 'required|integer|min:1',
            'maintenance_mode'        => 'required|boolean',
            /* fichiers : JPEG / PNG / JPG / WEBP / AVIF / GIF */
            'site_logo'       => 'nullable|image|',
            'homepage_banner' => 'nullable|image|',
        ]);

        $setting = Configuration::firstOrFail();

        /* 2. Gestion du logo */
        if ($request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
            // Supprimer l’ancien fichier s’il existe
            if ($setting->site_logo) {
                Storage::disk('public')->delete($setting->site_logo);
            }

            // Nouveau nom de fichier
            $filename = Str::slug($validated['site_name']) . '-logo-' . uniqid() . '.webp';
            $path = 'logos/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('site_logo'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $validated['site_logo'] = $path;
        }

        /* 3. Gestion du banner */
        if ($request->hasFile('homepage_banner') && $request->file('homepage_banner')->isValid()) {
            // Supprimer l’ancien fichier s’il existe
            if ($setting->homepage_banner) {
                Storage::disk('public')->delete($setting->homepage_banner);
            }

            // Nouveau nom de fichier
            $filename = 'banner-' . uniqid() . '.webp';
            $path = 'banners/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('homepage_banner'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $validated['homepage_banner'] = $path;
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
