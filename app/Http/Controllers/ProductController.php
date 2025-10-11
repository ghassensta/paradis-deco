<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;

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
        // dd($request->all());
        /* ───── 1. Validation ────────────────────────────────────────── */
        $rules = [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',

            /* images multiples */
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => 'file|mimetypes:image/*|max:5120',

            /* anciennes images (édition) */
            'old_media_images' => 'nullable|string',
        ];

        $messages = [
            // ——— Nom
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.string' => 'Le nom du produit doit être une chaîne de caractères.',
            'name.max' => 'Le nom du produit ne peut pas dépasser :max caractères.',
            'name.unique' => 'Un produit portant ce nom existe déjà.',

            // ——— Prix
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être au moins égal à :min.',

            // ——— Stock
            'stock.required' => 'Le stock est obligatoire.',
            'stock.integer' => 'Le stock doit être un nombre entier.',
            'stock.min' => 'Le stock doit être au moins égal à :min.',

            // ——— Description
            'description.required' => 'La description du produit est obligatoire.',

            // ——— SEO (meta)
            'meta_title.max' => 'Le titre méta ne peut pas dépasser :max caractères.',
            'meta_keywords.max' => 'Les mots-clés méta ne peuvent pas dépasser :max caractères.',
            'meta_description.max' => 'La description méta ne peut pas dépasser :max caractères.',

            // ——— Activation
            'is_active.boolean' => 'Le champ “actif” doit être vrai ou faux.',

            // ——— Images
            'mediaimage.array' => 'Les images doivent être envoyées sous forme de tableau.',
            'mediaimage.max' => 'Vous ne pouvez pas envoyer plus de :max images.',
            'mediaimage.*.file' => 'Chaque fichier doit être une image valide.',
            'mediaimage.*.mimetypes' => 'Le format des images doit être JPEG, PNG, GIF, WEBP, BMP ou SVG.',
            'mediaimage.*.max' => 'Chaque image ne doit pas dépasser :max kilo-octets (5 Mo).',

            // ——— Anciennes images
            'old_media_images.string' => 'Les anciennes images doivent être une chaîne JSON valide.',
        ];

        $validated = $request->validate($rules, $messages);


        /* ───── 2. Gestion des images multiples ──────────────────────── */
        $images = [];

        // a) images déjà présentes (mode édition)
        $images = !empty($validated['old_media_images'])
            ? array_filter(json_decode($validated['old_media_images'], true) ?: [])
            : [];


        // b) nouvelles images uploadées
        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                // Générer un nom unique avec l'horodatage et le nom original
                $imageName = time() . '_' . $file->getClientOriginalName();
                // Déplacer le fichier directement dans public/products
                $file->move(public_path('storage/products'), $imageName);
                // Stocker le chemin relatif dans le tableau
                $images[] = 'products/' . $imageName;
            }
        }
        //dd($images);
        /* ───── 4. Création du produit ───────────────────────────────── */
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'images' => $images,
            'category_ids' => $request->category_ids ?? [],           // cast JSON dans le modèle
        ]);

        /* ───── 5. Redirection ───────────────────────────────────────── */
        return redirect()
            ->route('produits.index')
            ->with('message', "Produit « {$product->name} » créé avec succès.");
    }
    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {

    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.edit', ["product" => $product, 'categories' => $categories]);
    }

    public function update(Request $request, $id)
    {

        //  dd($request->all());
        $validatedData = $request->validate([
            'mediaimage.*' => 'required|file',
            'old_media_images' => 'nullable',
        ], [
            'mediaimage.*.required' => 'Chaque fichier d\'image est obligatoire.',
            'mediaimage.*.file' => 'Chaque élément dans le champ des images doit être un fichier valide.',
            'old_media_images.nullable' => 'Les anciennes images peuvent être laissées vides.',
        ]);
        //dd()
        $product = Product::findOrFail($id);
        // dd($request->all());

        $oldImages = !empty($request->old_media_images)
            ? explode(',', $request->old_media_images)
            : [];

        $updatedFiles = array_filter($oldImages, function ($image) {
            return strpos($image, 'products/') === 0;
        });
        // dd($updatedFiles);

        $mediaImages = [];
        if ($request->hasFile('mediaimage')) {
            if (!file_exists(public_path('storage/products'))) {
            }
            foreach ($request->file('mediaimage') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/products'), $filename);
                $mediaImages[] = 'products/' . $filename;
            }
        }

        $sliderImages = array_merge($updatedFiles, $mediaImages);
        $sliderImages = array_filter($sliderImages);
        $product->name = $request->name ?? null;
        $product->price = $request->price ?? null;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description ?? null;
        $product->meta_title = $request->meta_title ?? null;
        $product->meta_keywords = $request->meta_keywords ?? null;
        $product->meta_description = $request->meta_description ?? null;
        $product->is_active = $request->is_active ?? false;
        $product->images = $sliderImages ?? null;
        $product->category_ids = $request->category_ids;
        $product->save();
        return redirect()
            ->route('produits.index')
            ->with('message', 'Produit mis à jour avec succès !');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json();
    }
}
