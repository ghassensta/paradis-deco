<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

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
     * Store a new category.
     */
    public function store(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|',
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // 2. Process and store the image as WebP
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = uniqid() . '_' . time() . '.webp';
            $path = 'categories/' . $filename;

            // Convert to WebP using Intervention Image
            $image = Image::read($file)->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $data['image'] = $path; // Store relative path
        } else {
            return response()->json(['error' => 'Invalid image file'], 400);
        }

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
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|image|',
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // 2. Process and store the image as WebP if provided
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if it exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $file = $request->file('image');
            $filename = uniqid() . '_' . time() . '.webp';
            $path = 'categories/' . $filename;

            // Convert to WebP using Intervention Image
            $image = Image::read($file)->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $data['image'] = $path; // Store relative path
        }

        // 3. Prepare fields for update
        $updatePayload = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'is_active' => (bool) $data['is_active'],
        ];

        // Add image to payload only if it was uploaded
        if (isset($data['image'])) {
            $updatePayload['image'] = $data['image'];
        }

        // 4. Update the model
        $category->update($updatePayload);

        // 5. Return response
        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category->fresh(),
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
