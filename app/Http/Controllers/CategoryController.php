<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display the category index view.
     */
    public function index()
    {
        return view('admin.category.index');
    }

    /**
     * Get categories for DataTables.
     */


    /**
     * Store a new category.
     */

    public function store(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();          // only validated data

        // 2. Store the file (public disk points to storage/app/public)
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName(); // Génère un nom unique avec la date
        $image->move(public_path('storage/categories'), $imageName); // Déplace directement dans public/categories
        $data['image'] = 'categories/' . $imageName; // Stocke le chemin relatif dans $data

        // 3. Persist the model
        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'image' => $data['image'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }


    /**
     * Get a category for editing.
     */
    public function edit(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();   // Données sûres

        // 2. Gestion du fichier image si fourni
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Génère un nom unique avec la date
            $image->move(public_path('storage/categories'), $imageName); // Déplace directement dans public/categories
            $data['image'] = 'categories/' . $imageName; // Stocke le chemin relatif dans $data
        }

        // 3. Préparation des champs à mettre à jour
        $updatePayload = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'is_active' => (bool) $data['is_active'],
        ];

        // Ajouter la clé 'image' uniquement si elle est dans $data
        if (isset($data['image'])) {
            $updatePayload['image'] = $data['image'];
        }

        // 4. Mise à jour du modèle
        $category->update($updatePayload);

        // 5. Réponse JSON
        return response()->json([
            'message' => 'Catégorie mise à jour avec succès.',
            'category' => $category->fresh(),   // renvoie les données à jour
        ]);
    }
    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        // Delete the image file if it exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
