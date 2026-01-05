<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.products.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view("admin.products.create", ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $rules = [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_avant' => 'required|image|',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => 'image|',
            'old_media_images' => 'nullable|string',
        ];

        $messages = [
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.string' => 'Le nom du produit doit être une chaîne de caractères.',
            'name.max' => 'Le nom du produit ne peut pas dépasser :max caractères.',
            'name.unique' => 'Un produit portant ce nom existe déjà.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être au moins égal à :min.',
            'stock.required' => 'Le stock est obligatoire.',
            'stock.integer' => 'Le stock doit être un nombre entier.',
            'stock.min' => 'Le stock doit être au moins égal à :min.',
            'image_avant.required' => 'L\'image de couverture est obligatoire.',
            'image_avant.image' => 'L\'image de couverture doit être une image valide.',
            'image_avant.mimes' => 'L\'image de couverture doit être de type JPEG, PNG, JPG, GIF ou SVG.',
            'image_avant.max' => 'L\'image de couverture ne doit pas dépasser 2 Mo.',
            'description.required' => 'La description du produit est obligatoire.',
            'meta_title.max' => 'Le titre méta ne peut pas dépasser :max caractères.',
            'meta_keywords.max' => 'Les mots-clés méta ne peuvent pas dépasser :max caractères.',
            'meta_description.max' => 'La description méta ne peut pas dépasser :max caractères.',
            'is_active.boolean' => 'Le champ “actif” doit être vrai ou faux.',
            'category_ids.required' => 'Vous devez sélectionner au moins une catégorie.',
            'category_ids.min' => 'Vous devez sélectionner au moins une catégorie.',
            'category_ids.*.exists' => 'Une ou plusieurs catégories sélectionnées sont invalides.',
            'mediaimage.array' => 'Les images doivent être envoyées sous forme de tableau.',
            'mediaimage.max' => 'Vous ne pouvez pas envoyer plus de :max images.',
            'mediaimage.*.image' => 'Chaque fichier doit être une image valide.',
            'mediaimage.*.mimes' => 'Le format des images doit être JPEG, PNG, GIF, WEBP, BMP ou SVG.',
            'mediaimage.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
            'old_media_images.string' => 'Les anciennes images doivent être une chaîne JSON valide.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // 2. Process cover image (image_avant) as WebP
        $coverImagePath = null;
        if ($request->hasFile('image_avant') && $request->file('image_avant')->isValid()) {
            $file = $request->file('image_avant');
            $filename = uniqid() . '_' . time() . '.webp';
            $path = 'products/cover/' . $filename;
            $image = Image::read($file)->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);
            $coverImagePath = $path;
        } else {
            return redirect()->back()->withErrors(['image_avant' => 'L\'image de couverture est invalide.'])->withInput();
        }

        // 3. Process multiple images (mediaimage) as WebP
        $images = [];
        if (!empty($validated['old_media_images'])) {
            $images = array_filter(json_decode($validated['old_media_images'], true) ?: []);
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $filename = uniqid() . '_' . time() . '.webp';
                    $path = 'products/media/' . $filename;
                    $image = Image::read($file)->toWebp(80);
                    Storage::disk('public')->put($path, (string) $image);
                    $images[] = $path;
                }
            }
        }

        // 4. Create the product
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'image_avant' => $coverImagePath,
            'images' => $images,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'category_ids' => $validated['category_ids'] ?? [], // Store as JSON
        ]);

        // 5. Redirect
        return redirect()
            ->route('produits.index')
            ->with('message', "Produit « {$product->name} » créé avec succès.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        // Implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', ['product' => $product, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 1. Validation
        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_avant' => 'sometimes|image|',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => 'image|',
            'old_media_images' => 'nullable|string',
        ];

        $messages = [
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.string' => 'Le nom du produit doit être une chaîne de caractères.',
            'name.max' => 'Le nom du produit ne peut pas dépasser :max caractères.',
            'name.unique' => 'Un produit portant ce nom existe déjà.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être au moins égal à :min.',
            'stock.required' => 'Le stock est obligatoire.',
            'stock.integer' => 'Le stock doit être un nombre entier.',
            'stock.min' => 'Le stock doit être au moins égal à :min.',
            'image_avant.image' => 'L\'image de couverture doit être une image valide.',
            'image_avant.mimes' => 'L\'image de couverture doit être de type JPEG, PNG, JPG, GIF ou SVG.',
            'image_avant.max' => 'L\'image de couverture ne doit pas dépasser 2 Mo.',
            'description.required' => 'La description du produit est obligatoire.',
            'meta_title.max' => 'Le titre méta ne peut pas dépasser :max caractères.',
            'meta_keywords.max' => 'Les mots-clés méta ne peuvent pas dépasser :max caractères.',
            'meta_description.max' => 'La description méta ne peut pas dépasser :max caractères.',
            'is_active.boolean' => 'Le champ “actif” doit être vrai ou faux.',
            'category_ids.required' => 'Vous devez sélectionner au moins une catégorie.',
            'category_ids.min' => 'Vous devez sélectionner au moins une catégorie.',
            'category_ids.*.exists' => 'Une ou plusieurs catégories sélectionnées sont invalides.',
            'mediaimage.array' => 'Les images doivent être envoyées sous forme de tableau.',
            'mediaimage.max' => 'Vous ne pouvez pas envoyer plus de :max images.',
            'mediaimage.*.image' => 'Chaque fichier doit être une image valide.',
            'mediaimage.*.mimes' => 'Le format des images doit être JPEG, PNG, GIF, WEBP, BMP ou SVG.',
            'mediaimage.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
            'old_media_images.string' => 'Les anciennes images doivent être une chaîne JSON valide.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // 2. Process cover image (image_avant) as WebP
        if ($request->hasFile('image_avant') && $request->file('image_avant')->isValid()) {
            // Delete old cover image if it exists
            if ($product->image_avant) {
                Storage::disk('public')->delete($product->image_avant);
            }
            $file = $request->file('image_avant');
            $filename = uniqid() . '_' . time() . '.webp';
            $path = 'products/cover/' . $filename;
            $image = Image::read($file)->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);
            $validated['image_avant'] = $path;
        }

        // 3. Process multiple images (mediaimage) as WebP
        $images = [];
        if (!empty($validated['old_media_images'])) {
            $images = array_filter(json_decode($validated['old_media_images'], true) ?: []);
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $filename = uniqid() . '_' . time() . '.webp';
                    $path = 'products/media/' . $filename;
                    $image = Image::read($file)->toWebp(80);
                    Storage::disk('public')->put($path, (string) $image);
                    $images[] = $path;
                }
            }
        }

        // 4. Update the product
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'image_avant' => $validated['image_avant'] ?? $product->image_avant,
            'images' => $images,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', $product->is_active),
            'category_ids' => $validated['category_ids'] ?? [], // Store as JSON
        ]);

        // 5. Redirect
        return redirect()
            ->route('produits.index')
            ->with('message', "Produit « {$product->name} » mis à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Delete cover image
        if ($product->image_avant) {
            Storage::disk('public')->delete($product->image_avant);
        }

        // Delete multiple images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès.']);
    }
}
