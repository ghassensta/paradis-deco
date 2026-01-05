<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display categories page
     */
    public function index()
    {
        return view('admin.category.index');
    }

    /**
     * Data for datatable (server side)
     */
    public function get(Request $request)
    {
        $query = Category::query();

        return DataTables::of($query)
            ->addColumn('image', function ($row) {
                $url = $row->image ? asset('storage/' . $row->image) : asset('images/no-image.png');
                return '<img src="' . e($url) . '" alt="' . e($row->name) . '" width="50">';
            })
            ->addColumn('is_active', function ($row) {
                $class = $row->is_active ? 'bg-label-success' : 'bg-label-warning';
                $text  = $row->is_active ? 'Active' : 'Inactive';
                return '<span class="badge ' . $class . '">' . $text . '</span>';
            })
            ->addColumn('image_url', function ($row) {
                return $row->image ? asset('storage/' . $row->image) : null;
            })
            ->rawColumns(['image', 'is_active'])
            ->make(true);
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'            => 'required|image',
            'name'             => 'required|string|max:255|unique:categories,name',
            'is_active'        => 'required|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Image Upload
        $imagePath = null;

        if ($request->hasFile('image')) {
            $filename = Str::uuid() . '.webp';
            $path     = 'categories/' . $filename;

            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $imagePath = $path;
        }

        $category = Category::create([
            'name'             => $validated['name'],
            'slug'             => Str::slug($validated['name']),
            'image'            => $imagePath,
            'is_active'        => (bool) $validated['is_active'],
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

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
        return response()->json($category);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'image'            => 'nullable|image',
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'is_active'        => 'required|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Image Upload
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $filename = Str::uuid() . '.webp';
            $path     = 'categories/' . $filename;

            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $category->image = $path;
        }

        $category->update([
            'name'             => $validated['name'],
            'slug'             => Str::slug($validated['name']),
            'is_active'        => (bool) $validated['is_active'],
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

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
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
