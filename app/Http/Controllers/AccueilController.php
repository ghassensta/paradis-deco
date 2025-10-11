<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inspiration;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Avis;
use Illuminate\Support\Facades\Validator;
use App\Models\Configuration;
class AccueilController extends Controller
{
    public function nouveautes()
    {
        $latestProducts = Product::where('is_active', true)->latest()
            ->take(10)
            ->get();
            
        $latestCategorys = Category::where('is_active', true)->latest()->take(4)->get();
        $inspirations = Inspiration::where('is_active', true)->latest()->take(4)->get();
        $testimonials = Avis::where('approved', true)
            ->with('product:id,name')
            ->latest()
            ->take(3)
            ->get();

        return view('front-office.acceuil.index', [
            'latestProducts' => $latestProducts,
            'latestCategories' => $latestCategorys,
            'inspirations' => $inspirations,
            'testimonials' => $testimonials
        ]);
    }


    public function InspirationShow($slug)
    {
        $inspiration = Inspiration::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedInspirations = Inspiration::where('is_active', true)
            ->where('id', '!=', $inspiration->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('front-office.inspirations.index', compact('inspiration', 'relatedInspirations'));
    }

    public function ProduitShow($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $categories = $product->category_ids;

        $similarProducts = collect();

        if (is_array($categories) && count($categories)) {
            $similarProducts = Product::where('id', '!=', $product->id)
                ->where(function ($query) use ($categories) {
                    foreach ($categories as $categoryId) {
                        $query->orWhereJsonContains('category_ids', $categoryId);
                    }
                })
                ->take(4)
                ->get();
        }

        // Fetch approved reviews for the product
        $reviews = Avis::where('product_id', $product->id)
            ->where('approved', true)
            ->latest()
            ->get();

        // Calculate average rating
        $averageRating = $reviews->avg('rating') ?? 0;
        $averageRating = number_format($averageRating, 1);

        // Calculate rating distribution
        $totalReviews = $reviews->count();
        $ratingDistribution = [
            5 => $totalReviews > 0 ? ($reviews->where('rating', 5)->count() / $totalReviews) * 100 : 0,
            4 => $totalReviews > 0 ? ($reviews->where('rating', 4)->count() / $totalReviews) * 100 : 0,
            3 => $totalReviews > 0 ? ($reviews->where('rating', 3)->count() / $totalReviews) * 100 : 0,
            2 => $totalReviews > 0 ? ($reviews->where('rating', 2)->count() / $totalReviews) * 100 : 0,
            1 => $totalReviews > 0 ? ($reviews->where('rating', 1)->count() / $totalReviews) * 100 : 0,
        ];

        return view('front-office.produit.index', [
            'product' => $product,
            'similarProducts' => $similarProducts,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'ratingDistribution' => $ratingDistribution,
            'totalReviews' => $totalReviews,
        ]);
    }

    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $avis = Avis::create([
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'name' => $request->name ?: 'Anonyme',
            'location' => $request->location,
            'approved' => false, // Pending approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre avis ! Il sera affiché après modération.'
        ]);
    }

    private function sidebarCategories()
    {
        return Category::query()
            ->where('is_active', true)
            ->select('categories.*')
            ->selectRaw(
                "(SELECT COUNT(*)
                        FROM products
                    WHERE products.is_active = 1
                        AND JSON_CONTAINS(
                            products.category_ids,
                            JSON_QUOTE(CAST(categories.id AS CHAR))
                        )
                    ) AS products_count"
            )
            ->orderByDesc('products_count')
            ->limit(4)
            ->get();
    }

    public function AllProduits()
    {
        return view('front-office.produit.allproduits', [
            'products' => Product::active()->latest()->paginate(12),
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => null,
            'freeShippingLimit' => config('shop.free_shipping_limit'),
        ]);
    }

    public function CategorieProduits($slug)
    {
        $selectedCategory = Category::where('slug', $slug)->firstOrFail();

        $products = Product::active()
            ->where(fn($q) => $q->whereJsonContains('category_ids', $selectedCategory->id)
                ->orWhereJsonContains('category_ids', (string) $selectedCategory->id))
            ->latest()
            ->paginate(12);

        return view('front-office.produit.allproduits', [
            'products' => $products,
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => $selectedCategory,
            'freeShippingLimit' => config('shop.free_shipping_limit'),
        ]);
    }
}
