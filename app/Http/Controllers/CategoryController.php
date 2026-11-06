<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\FaqSync;
use App\Support\ImageStorage;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    /**
     * Display categories page
     */
    public function index()
    {
        return view('admin.category.index', [
            'parents' => Category::roots()->ordered()->get(),
        ]);
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules($request));

        $this->validateParent($validator, $request, null);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $category = Category::create([
            'parent_id'        => $validated['parent_id'] ?? null,
            'name'             => $validated['name'],
            'slug'             => Category::uniqueSlug($validated['name']),
            'description_courte' => ($validated['description_courte'] ?? null) ?: null,
            'description'      => RichText::toHtml($validated['description'] ?? null) ?: null,
            'image'            => $this->storeImage($request),
            'is_active'        => (bool) $validated['is_active'],
            'sort_order'       => (int) ($validated['sort_order'] ?? 0),
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

        FaqSync::apply($category, $request->input('faqs'));

        return response()->json([
            'message'  => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    /**
     * Get category for edit
     */
    public function edit(Category $category)
    {
        // ajouter l’URL de l’image pour l’aperçu
        $category->image_url = $category->image ? asset('storage/' . $category->image) : null;

        $category->setRelation('faqs', $category->faqs()->get());

        return response()->json($category);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), $this->rules($request, $category));

        $this->validateParent($validator, $request, $category);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if ($path = $this->storeImage($request)) {
            ImageStorage::delete($category->image);
            $category->image = $path;
        }

        // Le slug n'est volontairement pas régénéré : les URLs /collections/…
        // sont indexées, les renommer casserait le référencement acquis.
        $category->update([
            'parent_id'        => $validated['parent_id'] ?? null,
            'name'             => $validated['name'],
            'description_courte' => ($validated['description_courte'] ?? null) ?: null,
            'description'      => RichText::toHtml($validated['description'] ?? null) ?: null,
            'is_active'        => (bool) $validated['is_active'],
            'sort_order'       => (int) ($validated['sort_order'] ?? 0),
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

        FaqSync::apply($category, $request->input('faqs'));

        return response()->json([
            'message'  => 'Catégorie mise à jour avec succès',
            'category' => $category->fresh()
        ]);
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return response()->json([
                'message' => 'Cette catégorie contient des sous-catégories. Supprimez-les ou rattachez-les ailleurs au préalable.',
            ], 422);
        }

        ImageStorage::delete($category->image);

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }

    /* =======================
     | Helpers
     ======================= */

    private function rules(Request $request, ?Category $category = null): array
    {
        $unique = 'unique:categories,name' . ($category ? ',' . $category->id : '');

        // Une catégorie principale porte l'illustration affichée en front ;
        // une sous-catégorie s'affiche sous forme de lien et n'en exige pas.
        $imageRule = ($category === null && ! $request->filled('parent_id'))
            ? ImageStorage::rule(required: true)
            : ImageStorage::rule();

        return array_merge(FaqSync::rules(), [
            'parent_id'        => 'nullable|integer|exists:categories,id',
            'image'            => $imageRule,
            'name'             => ['required', 'string', 'max:255', $unique],
            'description_courte' => 'nullable|string|max:600',
            'description'      => 'nullable|string|max:20000',
            'is_active'        => 'required|boolean',
            'sort_order'       => 'nullable|integer|min:0|max:65535',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);
    }

    /** Interdit l'auto-parentage, les boucles et les hiérarchies à trois niveaux. */
    private function validateParent($validator, Request $request, ?Category $category): void
    {
        $validator->after(function ($validator) use ($request, $category) {
            $parentId = $request->filled('parent_id') ? (int) $request->input('parent_id') : null;

            if ($parentId === null) {
                return;
            }

            $subject = $category ?? new Category();

            if (! $subject->canHaveParent($parentId)) {
                $validator->errors()->add(
                    'parent_id',
                    'Parent invalide : une catégorie ne peut pas être son propre parent, '
                    . 'ni être rattachée à une sous-catégorie, ni devenir sous-catégorie si elle a déjà des enfants.'
                );
            }
        });
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return ImageStorage::store($request->file('image'), 'categories', $request->input('name'));
    }
}
