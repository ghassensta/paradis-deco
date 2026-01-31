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
    // Récupérer le produit avec les avis approuvés
    $product = Product::where('slug', $slug)
        ->where('is_active', true)
        ->with(['avis' => function($query) {
            $query->where('approved', true)->latest();
        }])
        ->firstOrFail();

    // Calcul des avis approuvés
    $reviews = $product->avis; // déjà préchargé avec "with"
    $totalReviews = $reviews->count();
    $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

    // Distribution des notes
    $ratingDistribution = [];
    for ($i = 5; $i >= 1; $i--) {
        $count = $reviews->where('rating', $i)->count();
        $ratingDistribution[$i] = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0;
    }

    // Produits similaires par catégories
    $categories = $product->category_ids;
    $similarProducts = collect();
    if (is_array($categories) && count($categories) > 0) {
        $similarProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($categories) {
                foreach ($categories as $categoryId) {
                    $query->orWhereJsonContains('category_ids', $categoryId);
                }
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }

    $images = $product->images ? json_decode($product->images, true) : [];

    return view('front-office.produit.index', [
        'product' => $product,
        'similarProducts' => $similarProducts,
        'reviews' => $reviews,
        'averageRating' => $averageRating,
        'ratingDistribution' => $ratingDistribution,
        'totalReviews' => $totalReviews,
         'images' => $images,
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
        $config = Configuration::first();
        return view('front-office.produit.allproduits', [
            'products' => Product::active()->latest()->paginate(12),
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => null,
            'freeShippingLimit' => $config?->free_shipping_threshold,
                    'config'            => $config,

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
            $config = Configuration::first();
        return view('front-office.categorie.categorieproduits', [
            'products' => $products,
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => $selectedCategory,
            'freeShippingLimit' => $config?->free_shipping_threshold,
                    'config'            => $config,

        ]);
    }

    public function AllInspirations()
    {
        return view('front-office.inspirations.allinspirations', [
            'inspirations' => Inspiration::active()->latest()->paginate(10),
        ]);
    }
}
