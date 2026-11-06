<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inspiration;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Avis;
use App\Support\RichText;
use Illuminate\Support\Facades\Validator;
use App\Models\Configuration;

class AccueilController extends Controller
{
    public function nouveautes()
    {
        // Les compteurs d'avis sont agrégés en SQL : les données structurées
        // de la page d'accueil n'ont ainsi aucune requête par produit.
        $latestProducts = $this->productListQuery()
            ->latest()
            ->take(8)
            ->get();

        // Univers = catégories principales, dans l'ordre choisi en back-office.
        $rootCategories = Category::active()
            ->roots()
            ->ordered()
            ->take(8)
            ->get();

        $this->attachCategoryVisuals($rootCategories);

        return view('front-office.acceuil.index', [
            'latestProducts' => $latestProducts,
            'rootCategories' => $rootCategories,
            // Conservé : d'autres parties de la vue s'appuient encore dessus.
            'latestCategories' => $rootCategories->take(4),
            'bestSellers' => $this->bestSellers(),
            'inspirations' => Inspiration::active()->latest()->take(3)->get(),
            'testimonials' => Avis::where('approved', true)
                ->with('product:id,name,slug')
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }

    /** Requête commune aux listes de produits de la page d'accueil. */
    private function productListQuery()
    {
        return Product::active()
            ->withCount(['avis as approved_reviews_count' => fn ($q) => $q->where('approved', true)])
            ->withAvg(['avis as approved_reviews_avg' => fn ($q) => $q->where('approved', true)], 'rating');
    }

    /**
     * Meilleures ventes réelles, en une requête : jointure sur les lignes de
     * commande, commandes annulées exclues. Retourne une collection vide tant
     * qu'aucune vente n'existe — la section est alors masquée plutôt que de
     * présenter un classement inventé.
     */
    private function bestSellers(int $limit = 8)
    {
        return $this->productListQuery()
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('statuts', 'statuts.id', '=', 'orders.statut_id')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->where('statuts.name', 'not like', '%nnul%')
            ->groupBy('products.id')
            ->orderByDesc(DB::raw('SUM(order_items.quantity)'))
            ->select('products.*')
            ->take($limit)
            ->get();
    }

    /**
     * Donne une illustration à chaque catégorie : la sienne si elle en a une,
     * sinon la photo d'un de ses produits. Une seule requête pour l'ensemble,
     * et jamais d'image externe ni cassée.
     */
    private function attachCategoryVisuals($categories): void
    {
        $missing = $categories->filter(fn ($c) => blank($c->image));

        if ($missing->isEmpty()) {
            $categories->each(fn ($c) => $c->setAttribute('visual_url', $c->image_url));

            return;
        }

        // Pas de requête sur les sous-catégories : ProductController::
        // normalizeCategoryIds() rattache toujours un produit à la catégorie
        // parente en plus de sa sous-catégorie.
        $candidates = Product::active()
            ->whereNotNull('image_avant')
            ->where('image_avant', '!=', '')
            ->latest()
            ->take(120)
            ->get(['id', 'category_ids', 'image_avant']);

        foreach ($categories as $category) {
            if (filled($category->image)) {
                $category->setAttribute('visual_url', $category->image_url);
                continue;
            }

            $product = $candidates->first(
                fn ($p) => collect($p->category_ids)->map(fn ($id) => (int) $id)->contains((int) $category->id)
            );

            $category->setAttribute(
                'visual_url',
                $product ? Product::mediaUrl($product->image_avant) : asset('images/no-image.png')
            );
        }
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
        ->with(['activeVariants', 'avis' => function($query) {
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

    $images = $product->images ?? [];

    // FAQ affichables : une seule requête, filtrée en base.
    $productFaqs = $product->displayableFaqs();

    // Fil d'Ariane : Accueil > Catégorie > Sous-catégorie > Produit
    $primaryCategory = $product->primaryCategory();
    $breadcrumbs = [['name' => 'Accueil', 'url' => route('welcome')]];

    if ($primaryCategory) {
        foreach ($primaryCategory->ancestorsAndSelf() as $node) {
            $breadcrumbs[] = ['name' => $node->name, 'url' => $node->url];
        }
    }

    $breadcrumbs[] = ['name' => $product->name, 'url' => route('preview-article', $product->slug)];

    return view('front-office.produit.index', [
        'product' => $product,
        'breadcrumbs' => $breadcrumbs,
        'primaryCategory' => $primaryCategory,
        'productFaqs' => $productFaqs,
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
        // Seules les catégories principales alimentent la navigation latérale :
        // les sous-catégories sont accessibles depuis leur page parente.
        return Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->select('categories.*')
            ->selectRaw(
                "(SELECT COUNT(*)
                        FROM products
                    WHERE products.is_active = 1
                        AND JSON_CONTAINS(
                            products.category_ids,
                            CAST(categories.id AS CHAR)
                        )
                    ) AS products_count"
            )
            ->orderByDesc('products_count')
            ->limit(6)
            ->get();
    }

    public function AllProduits()
    {
        $config = Configuration::first();
        return view('front-office.produit.allproduits', [
            'products' => Product::active()
                ->withCount(['avis as approved_reviews_count' => fn ($q) => $q->where('approved', true)])
                ->withAvg(['avis as approved_reviews_avg' => fn ($q) => $q->where('approved', true)], 'rating')
                ->latest()
                ->paginate(12),
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => null,
            'freeShippingLimit' => $config?->free_shipping_threshold,
                    'config'            => $config,

        ]);
    }

    public function CategorieProduits($slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        // Une sous-catégorie atteinte en URL courte est redirigée vers son URL
        // canonique /collections/parent/enfant : une seule URL indexable.
        if (! $category->isRoot()) {
            return redirect()->route('categorie.sous-categorie', [
                $category->parent->slug,
                $category->slug,
            ], 301);
        }

        return $this->renderCategorie($category);
    }

    public function SousCategorieProduits($parentSlug, $slug)
    {
        $parent = Category::active()->roots()->where('slug', $parentSlug)->firstOrFail();

        $category = Category::active()
            ->where('parent_id', $parent->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderCategorie($category);
    }

    /** Rendu commun aux pages catégorie et sous-catégorie. */
    private function renderCategorie(Category $category)
    {
        // Une catégorie principale agrège aussi les produits de ses sous-catégories.
        $ids = $category->isRoot()
            ? $category->activeChildren()->pluck('id')->push($category->id)->all()
            : [$category->id];

        $products = Product::active()
            ->withCount(['avis as approved_reviews_count' => fn ($q) => $q->where('approved', true)])
            ->withAvg(['avis as approved_reviews_avg' => fn ($q) => $q->where('approved', true)], 'rating')
            ->where(function ($q) use ($ids) {
                foreach ($ids as $id) {
                    $q->orWhereJsonContains('category_ids', $id)
                        ->orWhereJsonContains('category_ids', (string) $id);
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $config = Configuration::first();

        return view('front-office.categorie.categorieproduits', [
            'products' => $products,
            'categories' => $this->sidebarCategories(),
            'selectedCategory' => $category,
            'subcategories' => $category->isRoot()
                ? $category->activeChildren()->ordered()->get()
                : collect(),
            'siblings' => $category->isRoot()
                ? collect()
                : $category->parent->activeChildren()->ordered()->where('id', '!=', $category->id)->get(),
            'breadcrumbs' => $this->breadcrumbsFor($category),
            'categoryFaqs' => $category->displayableFaqs(),
            'freeShippingLimit' => $config?->free_shipping_threshold,
            'config' => $config,
        ]);
    }

    /** Fil d'Ariane : Accueil > [catégorie parente] > catégorie courante. */
    private function breadcrumbsFor(Category $category): array
    {
        $items = [['name' => 'Accueil', 'url' => route('welcome')]];

        foreach ($category->ancestorsAndSelf() as $node) {
            $items[] = ['name' => $node->name, 'url' => $node->url];
        }

        return $items;
    }

    public function AllInspirations()
    {
        return view('front-office.inspirations.allinspirations', [
            'inspirations' => Inspiration::active()->latest()->paginate(10),
        ]);
    }

    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        // Le formulaire attend toujours un 200 : il lit data.success puis data.errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $data = $validator->validated();

        // Un avis est du texte : aucune balise n'est conservée.
        $data['comment'] = RichText::toText($data['comment']);
        $data['name'] = RichText::toText($data['name']);
        $data['location'] = RichText::toText($data['location'] ?? null) ?: null;

        Avis::create($data + ['approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Merci ! Votre avis sera publié après validation.',
        ]);
    }
}
