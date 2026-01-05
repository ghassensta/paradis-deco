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
    public function index()
    {
        return view('admin.products.index');
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_avant' => 'required|image',
            'description' => 'required|string',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => 'image',
            'old_media_images' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        /** ---------------- Cover Image ---------------- */
        $coverImagePath = null;

        if ($request->hasFile('image_avant')) {
            $filename = Str::uuid() . '.webp';
            $path = 'products/cover/' . $filename;

            $image = Image::read($request->file('image_avant'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $coverImagePath = $path;
        }

        /** ---------------- Multiple Images ---------------- */
        $images = [];

        if (!empty($validated['old_media_images'])) {
            $images = json_decode($validated['old_media_images'], true) ?? [];
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.webp';
                    $path = 'products/media/' . $filename;

                    $image = Image::read($file)->toWebp(80);
                    Storage::disk('public')->put($path, (string) $image);

                    $images[] = $path;
                }
            }
        }

        Product::create([
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
            'category_ids' => $validated['category_ids'],
        ]);

        return redirect()->route('produits.index')
            ->with('message', 'Produit créé avec succès');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_avant' => 'nullable|image',
            'description' => 'required|string',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'mediaimage' => 'nullable|array|max:5',
            'mediaimage.*' => 'image',
            'old_media_images' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];

        $validated = $request->validate($rules);

        /** ---------------- Cover Image ---------------- */
        if ($request->hasFile('image_avant')) {
            if ($product->image_avant) {
                Storage::disk('public')->delete($product->image_avant);
            }

            $filename = Str::uuid() . '.webp';
            $path = 'products/cover/' . $filename;

            $image = Image::read($request->file('image_avant'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $product->image_avant = $path;
        }

        /** ---------------- Multiple Images ---------------- */
        $images = [];

        if (!empty($validated['old_media_images'])) {
            $images = json_decode($validated['old_media_images'], true) ?? [];
        }

        if ($request->hasFile('mediaimage')) {
            foreach ($request->file('mediaimage') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.webp';
                    $path = 'products/media/' . $filename;

                    $image = Image::read($file)->toWebp(80);
                    Storage::disk('public')->put($path, (string) $image);

                    $images[] = $path;
                }
            }
        }

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'images' => $images,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'is_active' => $request->boolean('is_active', $product->is_active),
            'category_ids' => $validated['category_ids'],
        ]);

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

        $product->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }
}
