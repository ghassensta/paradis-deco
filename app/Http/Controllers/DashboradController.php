<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboradController extends Controller
{
    /** Périodes proposées par le filtre, en jours. */
    private const PERIODS = [
        '7' => '7 derniers jours',
        '30' => '30 derniers jours',
        '90' => '90 derniers jours',
        '365' => '12 derniers mois',
    ];

    private ?int $cancelledStatutId = null;

    /** Seuil au-delà duquel le stock n'est plus considéré comme faible. */
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(Request $request)
    {
        $days = (int) ($request->input('period', 30));
        $days = array_key_exists((string) $days, self::PERIODS) ? $days : 30;

        $end = CarbonImmutable::now()->endOfDay();
        $start = $end->subDays($days - 1)->startOfDay();
        $previousStart = $start->subDays($days);
        $previousEnd = $start->subSecond();

        // Les trois périodes (courante, précédente, aujourd'hui) sont calculées
        // en deux requêtes agrégées plutôt que six.
        $windows = [
            'current' => [$start, $end],
            'previous' => [$previousStart, $previousEnd],
            'today' => [CarbonImmutable::today(), $end],
        ];

        $totals = $this->orderTotalsFor($windows);
        $current = $totals['current'];
        $previous = $totals['previous'];

        $catalogue = $this->catalogueStats();
        $salesByProduct = $this->salesByProduct($start, $end);

        return view('admin.dashborad', [
            'periods' => self::PERIODS,
            'period' => $days,
            'periodLabel' => self::PERIODS[(string) $days],
            'rangeStart' => $start,
            'rangeEnd' => $end,

            'today' => $totals['today'],
            'current' => $current,
            'previous' => $previous,
            'trends' => [
                'revenue' => $this->trend($current['revenue'], $previous['revenue']),
                'orders' => $this->trend($current['orders'], $previous['orders']),
                'aov' => $this->trend($current['aov'], $previous['aov']),
                'units' => $this->trend($current['units'], $previous['units']),
            ],

            'series' => $this->timeSeries($start, $end, $days),
            'catalogue' => $catalogue,
            'topProducts' => $this->topProducts($salesByProduct),
            'topCategories' => $this->topCategories($salesByProduct),
            'watchList' => $this->watchList($salesByProduct),
            'recentOrders' => $this->recentOrders(),
            'alerts' => $this->alerts($catalogue),
            'seoScore' => $this->seoScore($catalogue),
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }

    /* =======================================================
     | Agrégats commandes
     ======================================================= */

    /**
     * Chiffre d'affaires, commandes, unités et panier moyen sur une période.
     * Les commandes annulées sont exclues : elles ne représentent pas du CA.
     *
     * @return array{revenue: float, orders: int, units: int, aov: float}
     */
    /**
     * Agrège plusieurs fenêtres temporelles en deux requêtes : une pour les
     * commandes, une pour les unités vendues (jointure order_items, qui
     * multiplierait les lignes et fausserait la somme du total).
     *
     * @param  array<string, array{0: CarbonImmutable, 1: CarbonImmutable}>  $windows
     * @return array<string, array{revenue: float, orders: int, units: int, aov: float}>
     */
    private function orderTotalsFor(array $windows): array
    {
        $ordersQuery = $this->validOrders();
        $unitsQuery = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->where('orders.statut_id', '!=', $this->cancelledStatutId());

        $bindings = [];

        foreach ($windows as $key => [$from, $to]) {
            $ordersQuery->selectRaw(
                "SUM(CASE WHEN orders.created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS {$key}_orders",
                [$from, $to]
            )->selectRaw(
                "COALESCE(SUM(CASE WHEN orders.created_at BETWEEN ? AND ? THEN orders.total_ttc ELSE 0 END), 0) AS {$key}_revenue",
                [$from, $to]
            );

            $unitsQuery->selectRaw(
                "COALESCE(SUM(CASE WHEN orders.created_at BETWEEN ? AND ? THEN order_items.quantity ELSE 0 END), 0) AS {$key}_units",
                [$from, $to]
            );

            $bindings[] = $key;
        }

        $orderRow = $ordersQuery->first();
        $unitRow = $unitsQuery->first();

        $result = [];

        foreach ($bindings as $key) {
            $orders = (int) ($orderRow->{$key . '_orders'} ?? 0);
            $revenue = (float) ($orderRow->{$key . '_revenue'} ?? 0);

            $result[$key] = [
                'revenue' => $revenue,
                'orders' => $orders,
                'units' => (int) ($unitRow->{$key . '_units'} ?? 0),
                // Division protégée : aucune commande ne doit pas planter.
                'aov' => $orders > 0 ? round($revenue / $orders, 3) : 0.0,
            ];
        }

        return $result;
    }

    private function validOrders()
    {
        return Order::query()->where('statut_id', '!=', $this->cancelledStatutId());
    }

    private function cancelledStatutId(): int
    {
        // Mémorisé sur l'instance, pas en statique : le contrôleur est résolu
        // à chaque requête, alors qu'un static survivrait au processus
        // (tests, Octane) et figerait une valeur obsolète.
        return $this->cancelledStatutId ??= (int) (
            DB::table('statuts')->where('name', 'like', '%nnul%')->value('id') ?? 0
        );
    }

    /**
     * Évolution en pourcentage. Retourne null lorsque la période précédente
     * est vide : afficher « +100 % » serait trompeur.
     */
    private function trend(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /* =======================================================
     | Séries temporelles
     ======================================================= */

    /** @return array{labels: list<string>, revenue: list<float>, orders: list<int>, hasData: bool} */
    private function timeSeries(CarbonImmutable $start, CarbonImmutable $end, int $days): array
    {
        // Au-delà de 90 jours, l'agrégation se fait au mois pour rester lisible.
        $byMonth = $days > 90;
        $format = $byMonth ? '%Y-%m' : '%Y-%m-%d';

        $rows = $this->validOrders()
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$format}') AS bucket")
            ->selectRaw('COUNT(*) AS orders_count, COALESCE(SUM(orders.total_ttc), 0) AS revenue')
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');

        $labels = [];
        $revenue = [];
        $orders = [];
        $cursor = $byMonth ? $start->startOfMonth() : $start;

        while ($cursor <= $end) {
            $key = $cursor->format($byMonth ? 'Y-m' : 'Y-m-d');
            $bucket = $rows[$key] ?? null;

            $labels[] = $byMonth ? $cursor->translatedFormat('M Y') : $cursor->format('d/m');
            $orders[] = (int) ($bucket->orders_count ?? 0);
            $revenue[] = round((float) ($bucket->revenue ?? 0), 3);

            $cursor = $byMonth ? $cursor->addMonth() : $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $orders,
            'hasData' => array_sum($orders) > 0,
        ];
    }

    /* =======================================================
     | Catalogue
     ======================================================= */

    /** Un seul passage SQL pour tous les compteurs produits, catégories et avis. */
    private function catalogueStats(): array
    {
        $threshold = self::LOW_STOCK_THRESHOLD;

        $products = DB::table('products')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('SUM(is_active = 1) AS active')
            ->selectRaw("SUM(stock = 0) AS out_of_stock")
            ->selectRaw("SUM(stock > 0 AND stock <= {$threshold}) AS low_stock")
            ->selectRaw("SUM(description_courte IS NULL OR description_courte = '') AS without_short")
            ->selectRaw("SUM(description IS NULL OR description = '') AS without_description")
            ->selectRaw("SUM(meta_title IS NULL OR meta_title = '') AS without_meta_title")
            ->selectRaw("SUM(meta_description IS NULL OR meta_description = '') AS without_meta_description")
            ->selectRaw("SUM(image_avant IS NULL OR image_avant = '') AS without_image")
            ->selectRaw("SUM(category_ids IS NULL OR JSON_LENGTH(category_ids) = 0) AS without_category")
            ->first();

        $categories = DB::table('categories')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('SUM(is_active = 1) AS active')
            ->selectRaw("SUM((description IS NULL OR description = '') AND (description_courte IS NULL OR description_courte = '')) AS without_description")
            ->selectRaw("SUM(meta_description IS NULL OR meta_description = '') AS without_meta_description")
            ->first();

        $reviews = DB::table('avis')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('SUM(approved = 1) AS approved')
            ->selectRaw('SUM(approved = 0) AS pending')
            ->first();

        $faqs = DB::table('faqs')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(faqable_type = ?) AS products", [Product::class])
            ->selectRaw("SUM(faqable_type = ?) AS categories", [Category::class])
            ->first();

        return [
            'products' => (array) $products,
            'categories' => (array) $categories,
            'reviews' => (array) $reviews,
            'faqs' => (array) $faqs,
            'clients' => DB::table('clients')->whereNull('deleted_at')->count(),
        ];
    }

    /* =======================================================
     | Ventes par produit / catégorie
     ======================================================= */

    /** Ventes agrégées par produit sur la période (une seule requête). */
    private function salesByProduct(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->where('orders.statut_id', '!=', $this->cancelledStatutId())
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id')
            ->selectRaw('SUM(order_items.quantity) AS units')
            ->selectRaw('SUM(order_items.subtotal) AS revenue')
            ->get()
            ->keyBy('product_id');
    }

    private function topProducts(Collection $sales, int $limit = 5): Collection
    {
        $ids = $sales->sortByDesc('units')->take($limit)->keys()->all();

        if (! $ids) {
            return collect();
        }

        return Product::whereIn('id', $ids)
            ->get(['id', 'name', 'slug', 'stock', 'price', 'image_avant'])
            ->map(fn (Product $p) => [
                'product' => $p,
                'units' => (int) $sales[$p->id]->units,
                'revenue' => (float) $sales[$p->id]->revenue,
            ])
            ->sortByDesc('units')
            ->values();
    }

    /**
     * Les produits sont rattachés aux catégories par une colonne JSON :
     * l'agrégation se fait en PHP à partir des ventes déjà chargées,
     * ce qui évite autant de requêtes que de catégories.
     */
    private function topCategories(Collection $sales, int $limit = 5): Collection
    {
        $categories = Category::query()
            ->select(['id', 'name', 'parent_id', 'slug'])
            ->get()
            ->keyBy('id');

        if ($categories->isEmpty() || $sales->isEmpty()) {
            return collect();
        }

        $products = Product::whereIn('id', $sales->keys())->get(['id', 'category_ids']);
        $totals = [];

        foreach ($products as $product) {
            $sale = $sales[$product->id];

            foreach ($product->category_ids as $categoryId) {
                $category = $categories[$categoryId] ?? null;

                // Seules les catégories principales sont agrégées : compter
                // parent et enfant compterait deux fois la même vente.
                if (! $category || $category->parent_id !== null) {
                    continue;
                }

                $totals[$categoryId] ??= ['category' => $category, 'units' => 0, 'revenue' => 0.0];
                $totals[$categoryId]['units'] += (int) $sale->units;
                $totals[$categoryId]['revenue'] += (float) $sale->revenue;
            }
        }

        return collect($totals)->sortByDesc('revenue')->take($limit)->values();
    }

    /** Produits nécessitant une action : rupture, stock faible, jamais vendus. */
    private function watchList(Collection $sales): array
    {
        $threshold = self::LOW_STOCK_THRESHOLD;

        $neverSold = Product::query()
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))
                ->from('order_items')
                ->whereColumn('order_items.product_id', 'products.id')
                ->whereNull('order_items.deleted_at'))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'stock', 'created_at']);

        return [
            'outOfStock' => Product::where('stock', 0)->orderBy('name')->limit(5)->get(['id', 'name', 'stock']),
            'lowStock' => Product::where('stock', '>', 0)->where('stock', '<=', $threshold)
                ->orderBy('stock')->limit(5)->get(['id', 'name', 'stock']),
            'neverSold' => $neverSold,
        ];
    }

    private function recentOrders(int $limit = 8): Collection
    {
        return Order::with(['client:id,name,city', 'statut:id,name'])
            ->latest('created_at')
            ->limit($limit)
            ->get(['id', 'numero_commande', 'client_id', 'statut_id', 'total_ttc', 'created_at']);
    }

    /* =======================================================
     | Alertes et score de complétude
     ======================================================= */

    private function alerts(array $catalogue): array
    {
        $p = $catalogue['products'];
        $c = $catalogue['categories'];
        $r = $catalogue['reviews'];

        $pending = (int) $this->validOrders()
            ->whereHas('statut', fn ($q) => $q->where('is_publish', false))
            ->count();

        $alerts = [
            ['level' => 'danger', 'count' => (int) $p['out_of_stock'], 'label' => 'produit(s) en rupture de stock', 'route' => route('produits.index')],
            ['level' => 'warning', 'count' => (int) $p['low_stock'], 'label' => 'produit(s) en stock faible', 'route' => route('produits.index')],
            ['level' => 'warning', 'count' => $pending, 'label' => 'commande(s) en cours de traitement', 'route' => route('commandes.index')],
            ['level' => 'warning', 'count' => (int) $r['pending'], 'label' => 'avis en attente de modération', 'route' => route('avis.index')],
            ['level' => 'info', 'count' => (int) $p['without_short'], 'label' => 'produit(s) sans description courte', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $p['without_description'], 'label' => 'produit(s) sans description longue', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $p['without_meta_title'], 'label' => 'produit(s) sans titre SEO', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $p['without_meta_description'], 'label' => 'produit(s) sans méta-description', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $p['without_image'], 'label' => 'produit(s) sans image', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $p['without_category'], 'label' => 'produit(s) sans catégorie', 'route' => route('produits.index')],
            ['level' => 'info', 'count' => (int) $c['without_description'], 'label' => 'catégorie(s) sans description', 'route' => route('categories.index')],
        ];

        // Seules les alertes réellement déclenchées sont affichées.
        return array_values(array_filter($alerts, fn ($a) => $a['count'] > 0));
    }

    /**
     * Score de complétude SEO du catalogue : part des champs éditoriaux
     * réellement renseignés. Ce n'est pas une mesure de position Google.
     */
    private function seoScore(array $catalogue): array
    {
        $p = $catalogue['products'];
        $c = $catalogue['categories'];

        $productTotal = (int) $p['total'];
        $categoryTotal = (int) $c['total'];

        $checks = [];

        if ($productTotal > 0) {
            $checks['Produits — description courte'] = 1 - ((int) $p['without_short'] / $productTotal);
            $checks['Produits — description longue'] = 1 - ((int) $p['without_description'] / $productTotal);
            $checks['Produits — titre SEO'] = 1 - ((int) $p['without_meta_title'] / $productTotal);
            $checks['Produits — méta-description'] = 1 - ((int) $p['without_meta_description'] / $productTotal);
            $checks['Produits — image'] = 1 - ((int) $p['without_image'] / $productTotal);
            $checks['Produits — catégorie'] = 1 - ((int) $p['without_category'] / $productTotal);
        }

        if ($categoryTotal > 0) {
            $checks['Catégories — description'] = 1 - ((int) $c['without_description'] / $categoryTotal);
            $checks['Catégories — méta-description'] = 1 - ((int) $c['without_meta_description'] / $categoryTotal);
        }

        return [
            'checks' => collect($checks)->map(fn ($v) => (int) round($v * 100))->all(),
            'global' => $checks ? (int) round(array_sum($checks) / count($checks) * 100) : null,
        ];
    }
}
