<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\FaqSync;
use App\Support\ImageStorage;
use App\Support\RichText;
use App\Support\VariantSync;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{


    public function index()
    {
        return view('admin.products.index');
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => $this->categoryTree(),
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeSlug($request);

        $rules = [
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'image_avant' => ImageStorage::rule(required: true),
            'description' => 'required|string',
            'description_courte' => 'nullable|string|max:600',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => ImageStorage::rule(),
            'old_media_images' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ];

        $rules = array_merge($rules, FaqSync::rules(), VariantSync::rules());

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        /** ---------------- Cover Image ---------------- */
        $coverImagePath = null;

        if ($request->hasFile('image_avant')) {
            $coverImagePath = ImageStorage::store(
                $request->file('image_avant'),
                'products/cover',
                $request->input('name')
            );
        }

        /** ---------------- Multiple Images ---------------- */
        $images = [];

        if (!empty($validated['old_media_images'])) {
            $images = json_decode($validated['old_media_images'], true) ?? [];
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $images[] = ImageStorage::store($file, 'products/media', $request->input('name'));
                }
            }
        }

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => ($validated['slug'] ?? null) ?: Product::uniqueSlug($validated['name']),
            'image_avant' => $coverImagePath,
            'images' => $images,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            // La colonne porte un index unique : '' collisionnerait entre produits.
            'sku' => ($validated['sku'] ?? null) ?: null,
            'description' => RichText::toHtml($validated['description']),
            'description_courte' => ($validated['description_courte'] ?? null) ?: null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'category_ids' => $this->normalizeCategoryIds($validated['category_ids']),

        ]);

        FaqSync::apply($product, $request->input('faqs'));

        // Le marqueur distingue « toutes les lignes supprimées » d'un envoi
        // qui ne vient pas de ce formulaire et ne doit rien effacer.
        if ($request->boolean('variants_present')) {
            VariantSync::apply($product, $validated['variants'] ?? null);
        }

        return redirect()->route('produits.index')
            ->with('message', 'Produit créé avec succès');
    }

    /** « Lampe Dorée 60 cm » devient lampe-doree-60-cm avant la validation d'unicité. */
    private function normalizeSlug(Request $request): void
    {
        if ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('slug'))]);
        }
    }

    /** Catégories principales avec leurs sous-catégories, pour les optgroup. */
    private function categoryTree()
    {
        return Category::active()
            ->roots()
            ->with(['activeChildren' => fn ($q) => $q->ordered()])
            ->ordered()
            ->get();
    }

    /**
     * Garantit la cohérence de l'arborescence : sélectionner une sous-catégorie
     * rattache implicitement le produit à sa catégorie parente.
     */
    private function normalizeCategoryIds(array $ids): array
    {
        $categories = Category::whereIn('id', $ids)->get(['id', 'parent_id']);

        return $categories
            ->flatMap(fn ($c) => array_filter([$c->id, $c->parent_id]))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $product->load('faqs', 'variants');
        $categories = $this->categoryTree();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->normalizeSlug($request);

        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $id,
            'image_avant' => ImageStorage::rule(),
            'description' => 'required|string',
            'description_courte' => 'nullable|string|max:600',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => ImageStorage::rule(),
            'old_media_images' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ];

        $rules = array_merge($rules, FaqSync::rules(), VariantSync::rules());

        $validated = $request->validate($rules);

        /** ---------------- Cover Image ---------------- */
        if ($request->hasFile('image_avant')) {
            $product->image_avant = ImageStorage::replace(
                $request->file('image_avant'),
                'products/cover',
                $product->image_avant,
                $request->input('name')
            );
        }

        /** ---------------- Multiple Images ---------------- */
        $images = [];

        if (!empty($validated['old_media_images'])) {
            $images = json_decode($validated['old_media_images'], true) ?? [];
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $images[] = ImageStorage::store($file, 'products/media', $request->input('name'));
                }
            }
        }

        // Le slug n'est jamais régénéré depuis le nom : seul un changement
        // explicite le modifie, et l'ancienne URL redirige alors en 301.
        if (filled($validated['slug'] ?? null)) {
            $product->changeSlug($validated['slug']);
        }

        $product->update([
            'name' => $validated['name'],
            'images' => $images,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'sku' => ($validated['sku'] ?? null) ?: null,
            'description' => RichText::toHtml($validated['description']),
            'description_courte' => ($validated['description_courte'] ?? null) ?: null,
            'is_active' => $request->boolean('is_active', $product->is_active),
            'category_ids' => $this->normalizeCategoryIds($validated['category_ids']),
               'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        FaqSync::apply($product, $request->input('faqs'));

        // Le marqueur distingue « toutes les lignes supprimées » d'un envoi
        // qui ne vient pas de ce formulaire et ne doit rien effacer.
        if ($request->boolean('variants_present')) {
            VariantSync::apply($product, $validated['variants'] ?? null);
        }

        return redirect()->route('produits.index')
            ->with('message', 'Produit mis à jour avec succès');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_avant) {
            Storage::disk('public')->delete($product->image_avant);
        }

        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->variants->each(fn ($variant) => ImageStorage::delete($variant->image));

        $product->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }
}
