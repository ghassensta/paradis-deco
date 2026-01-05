<?php

namespace App\Http\Controllers;

use App\Models\Inspiration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class InspirationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inspirations = Inspiration::latest()->paginate(10);
        return view('admin.inspirations.index', compact('inspirations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.inspirations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resume' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'image' => 'nullable|image|',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Inspiration::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // Gestion de l'image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $filename = Str::slug($data['title']) . '-' . time() . '.webp';
            $path = 'inspirations/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $data['image'] = $path;
        }

        Inspiration::create($data);

        return redirect()->route('inspirations.index')->with('message', 'Inspiration created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $inspiration = Inspiration::where('id', $id)->firstOrFail();
        return view('admin.inspirations.edit', compact('inspiration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resume' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'image' => 'nullable|image|',
            'is_active' => 'boolean',
        ]);

        $inspiration = Inspiration::where('id', $id)->firstOrFail();
        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug (sauf pour l'inspiration actuelle)
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Inspiration::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // Gestion de l'image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Supprimer l'ancienne image si elle existe
            if ($inspiration->image) {
                Storage::disk('public')->delete($inspiration->image);
            }

            $filename = Str::slug($data['title']) . '-' . time() . '.webp';
            $path = 'inspirations/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $data['image'] = $path;
        }

        $inspiration->update($data);

        return redirect()->route('inspirations.index')->with('message', 'Inspiration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inspiration = Inspiration::where('id', $id)->firstOrFail();
        if ($inspiration->image) {
            Storage::disk('public')->delete($inspiration->image);
        }
        $inspiration->delete();

        return response()->json([
            'message' => 'Inspiration deleted successfully.',
        ]);
    }

    /**
     * Toggle the is_active status of an inspiration.
     */
    public function toggleActive(Request $request, string $id)
    {
        $inspiration = Inspiration::where('id', $id)->firstOrFail();
        $inspiration->update([
            'is_active' => !$inspiration->is_active,
        ]);

        return response()->json([
            'message' => 'Inspiration status updated successfully.',
            'is_active' => $inspiration->is_active,
        ]);
    }
}
