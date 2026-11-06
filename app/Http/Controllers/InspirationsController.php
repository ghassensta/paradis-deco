<?php

namespace App\Http\Controllers;

use App\Models\Inspiration;
use App\Support\ImageStorage;
use App\Support\RichText;
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
            'resume' => 'nullable|string|max:2000',
            'description' => 'nullable|string|max:60000',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'image' => ImageStorage::rule(),
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Le contenu de l'éditeur est nettoyé côté serveur : une requête HTTP
        // directe ne doit pas pouvoir injecter de script dans les pages.
        $data['description'] = RichText::toHtml($data['description'] ?? null) ?: null;
        $data['resume'] = RichText::toText($data['resume'] ?? null) ?: null;

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Inspiration::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // Redimensionnement + conversion WebP + nom serveur : voir ImageStorage.
        if ($request->hasFile('image')) {
            $data['image'] = ImageStorage::store($request->file('image'), 'inspirations', $data['title']);
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
            'resume' => 'nullable|string|max:2000',
            'description' => 'nullable|string|max:60000',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string|',
            'image' => ImageStorage::rule(),
            'is_active' => 'boolean',
        ]);

        $inspiration = Inspiration::where('id', $id)->firstOrFail();
        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Le contenu de l'éditeur est nettoyé côté serveur : une requête HTTP
        // directe ne doit pas pouvoir injecter de script dans les pages.
        $data['description'] = RichText::toHtml($data['description'] ?? null) ?: null;
        $data['resume'] = RichText::toText($data['resume'] ?? null) ?: null;

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug (sauf pour l'inspiration actuelle)
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Inspiration::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // L'ancienne image n'est supprimée qu'une fois la nouvelle écrite.
        if ($request->hasFile('image')) {
            $data['image'] = ImageStorage::replace(
                $request->file('image'),
                'inspirations',
                $inspiration->image,
                $data['title']
            );
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
            ImageStorage::delete($inspiration->image);
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
