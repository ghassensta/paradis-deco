<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inspiration;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Avis;

class AjaxController extends Controller
{
public function getCommandes(Request $request)
{
    $draw = $request->input('draw', 1);
    $start = $request->input('start', 0);
    $rowPerPage = $request->input('length', 10);
    $searchValue = '';
    $statut_id = $request->input('statut_id');

    // Récupération des paramètres de tri
    $order = $request->input('order', []);
    $columnIndex = isset($order[0]['column']) ? $order[0]['column'] : 0;
    $columnSortOrder = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

    // Mapping des colonnes
    $columns = ['id', 'created_at', 'client_id', 'total_ttc', 'statut_id'];
    $orderColumn = $columns[$columnIndex] ?? 'created_at';

    // Récupération de la recherche
    $search = $request->input('search');
    if (is_array($search) && isset($search['value'])) {
        $searchValue = $search['value'];
    }

    // Construction de la requête optimisée
    $query = Order::select([
        'orders.id',
        'orders.numero_commande',
        'orders.created_at',
        'orders.client_id',
        'orders.statut_id',
        'orders.total_ttc',
        'orders.subtotal_ht',
        'orders.shipping_cost'
    ])
    ->with([
        'statut:id,name',
        'client:id,name,adresse,phone',
        'items' => function ($query) {
            $query->select('order_items.id', 'order_items.order_id', 'order_items.product_id', 'order_items.quantity', 'order_items.unit_price', 'order_items.subtotal')
                ->with(['product:id,name,images']);
        }
    ]);

    // Filtre de recherche
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('orders.numero_commande', 'like', '%' . $searchValue . '%')
              ->orWhereHas('client', function ($q) use ($searchValue) {
                  $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('phone', 'like', '%' . $searchValue . '%');
              });
        });
    }

    // Filtre par statut
    if ($statut_id && $statut_id !== 'all') {
        $query->where('statut_id', $statut_id);
    }

    // Total des enregistrements
    $totalRecords = Order::count();
    $totalFiltered = $query->count();

    // Application du tri et pagination
    $commandes = $query->orderBy($orderColumn, $columnSortOrder)
        ->offset($start)
        ->limit($rowPerPage)
        ->get();

    // Formatage des données pour DataTable
    $data_arr = $commandes->map(function ($row) {
        $clientImages = is_array($row->client->images ?? null) ? $row->client->images : [];
        $clientFirstImage = !empty($clientImages) ? $clientImages[0] : null;

        return [
            'id' => $row->id,
            'numero_commande' => $row->numero_commande,
            'date' => $row->created_at->format('d/m/Y'),
            'date_full' => $row->created_at->format('d/m/Y H:i'),
            'client_name' => optional($row->client)->name ?? 'N/A',
            'client_phone' => optional($row->client)->phone ?? 'N/A',
            'client_adresse' => optional($row->client)->adresse ?? 'N/A',
            'total_ttc' => number_format($row->total_ttc ?? 0, 3, '.', ''),
            'subtotal_ht' => number_format($row->subtotal_ht ?? 0, 3, '.', ''),
            'shipping_cost' => number_format($row->shipping_cost ?? 0, 3, '.', ''),
            'items_count' => $row->items->count(),
            'items' => $row->items->map(function($item) {
                $images = [];
                if ($item->product) {
                    $productImages = $item->product->images;
                    if (is_string($productImages)) {
                        $images = json_decode($productImages, true) ?? [];
                    } elseif (is_array($productImages)) {
                        $images = $productImages;
                    }
                }

                return [
                    'name' => optional($item->product)->name ?? 'Produit supprimé',
                    'image' => !empty($images) ? $images[0] : null,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price ?? 0, 3, '.', ''),
                    'subtotal' => number_format($item->subtotal ?? 0, 3, '.', ''),
                ];
            })->toArray(),
            'statut_name' => optional($row->statut)->name ?? 'N/A',
        ];
    })->toArray();

    return response()->json([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalFiltered,
        'data' => $data_arr,
    ]);
}

  public function getProducts(Request $request)
    {
        // 1) Paramètres DataTables avec valeurs par défaut
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $rowPerPage = (int) $request->input('length', 10);
        $orderArr = $request->input('order', []);
        $columnsArr = $request->input('columns', []);
        $searchValue = $request->input('search.value', '');

        $columnIndex = $orderArr[0]['column'] ?? 0;
        $columnSortOrder = $orderArr[0]['dir'] ?? 'asc';
        $columnName = $columnsArr[$columnIndex]['data'] ?? 'name';

        // Colonnes triables
        $sortable = [
            'id' => 'products.id',
            'image_avant' => 'products.image_avant', // Updated to support sorting by image_avant
            'images' => 'products.created_at', // Fallback
            'name' => 'products.name',
            'description' => 'products.description',
            'price' => 'products.price',
            'stock' => 'products.stock',
            'is_active' => 'products.is_active',
            'created_at' => 'products.created_at',
        ];
        $orderBy = $sortable[$columnName] ?? 'products.created_at';

        // 2) Requête filtrée
        $query = Product::query();

        if (!empty($searchValue)) {
            $query->where('products.name', 'like', "%{$searchValue}%");
        }

        $totalRecords = $query->count();

        $products = $query->orderBy($orderBy, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // 3) Précharger les noms des catégories
        $allCategories = Category::pluck('name', 'id')->toArray();

        // 4) Formatage DataTables
        $data = $products->map(function ($p) use ($allCategories) {
            $categoryNames = [];

            if (is_array($p->category_ids)) {
                foreach ($p->category_ids as $id) {
                    if (isset($allCategories[$id])) {
                        $categoryNames[] = $allCategories[$id];
                    }
                }
            }

            return [
                'id' => $p->id,
                'image_avant' => $p->image_avant, // Include cover image
                'images' => $p->images ?? [],
                'categories' => $categoryNames,
                'name' => $p->name,
                'description' => $p->description,
                'price' => $p->price,
                'stock' => $p->stock,
                'is_active' => $p->is_active,
                'meta_title' => $p->meta_title,
                'meta_description' => $p->meta_description,
                'meta_keywords' => $p->meta_keywords,
                'slug' => $p->slug,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }
    public function getCategory(Request $request)
    {
        // DataTables parameters
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $rowPerPage = (int) $request->input('length', 10);
        $orderArr = $request->input('order', []);
        $columnsArr = $request->input('columns', []);
        $searchValue = $request->input('search.value', '');

        // Sorting column
        $columnIndex = $orderArr[0]['column'] ?? 0;
        $columnSortOrder = $orderArr[0]['dir'] ?? 'asc';
        $columnName = $columnsArr[$columnIndex]['data'] ?? 'name';

        // Map DataTables columns to database columns
        $sortable = [
            'id' => 'id',
            'image' => 'image', // Changed from image_url
            'name' => 'name',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
        ];
        $orderBy = $sortable[$columnName] ?? 'created_at';

        // Build query
        $query = Category::select(['id', 'image', 'name', 'is_active', 'created_at']);

        if ($searchValue !== '') {
            $query->where('name', 'like', "%{$searchValue}%");
        }

        $totalRecords = $query->count();

        $categories = $query->orderBy($orderBy, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Format data for DataTables
        $data = $categories->map(fn($category) => [
            'id' => $category->id,
            'image' => $category->image_url ? '<img src="' . $category->image_url . '" width="50" alt="Category Image">' : '-',
            'name' => $category->name,
            'is_active' => $category->is_active ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-warning">Inactive</span>',
            'created_at' => $category->created_at->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // Update if adding more filters
            'data' => $data,
        ]);
    }

    public function getInspiration(Request $request)
    {
        // DataTables parameters
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $rowPerPage = (int) $request->input('length', 10);
        $orderArr = $request->input('order', []);
        $columnsArr = $request->input('columns', []);
        $searchValue = $request->input('search.value', '');

        // Sorting column
        $columnIndex = $orderArr[0]['column'] ?? 0;
        $columnSortOrder = $orderArr[0]['dir'] ?? 'asc';
        $columnName = $columnsArr[$columnIndex]['data'] ?? 'title';

        // Map DataTables columns to database columns
        $sortable = [
            'id' => 'id',
            'image' => 'image',
            'title' => 'title',
            'resume' => 'resume',
            'description' => 'description',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
        ];
        $orderBy = $sortable[$columnName] ?? 'created_at';

        // Build query
        $query = Inspiration::select(['id', 'slug', 'image', 'title', 'resume', 'description', 'is_active', 'created_at']);

        // Apply search filter
        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('resume', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        // Total records before filtering
        $totalRecords = Inspiration::count();
        // Total records after filtering
        $filteredRecords = $query->count();

        // Fetch paginated data
        $inspirations = $query->orderBy($orderBy, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Format data for DataTables
        $data = $inspirations->map(fn($inspiration) => [
            'id' => $inspiration->id,
            'image' => $inspiration->image && Storage::disk('public')->exists($inspiration->image)
                ? '<img src="' . Storage::url($inspiration->image) . '" width="50" alt="' . e($inspiration->title) . '">'
                : '-',
            'title' => $inspiration->title,
            'resume' => $inspiration->resume ? Str::limit(strip_tags($inspiration->resume), 50) : '-',
            'description' => $inspiration->description ? Str::limit(strip_tags($inspiration->description), 50) : '-',
            'is_active' => '<label class="switch "><input type="checkbox" class="toggle-active" data-id="' . $inspiration->id . '" ' . ($inspiration->is_active ? 'checked' : '') . '><span class="slider round"></span></label>',
            'created_at' => $inspiration->created_at->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

  public function getAvis(Request $request)
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $rowPerPage = $request->input('length', 10);
        $columnIndex = 0;
        $columnName = 'id';
        $columnSortOrder = 'asc';
        $searchValue = '';
        $product_id = $request->input('product_id');

        // Order information
        $order = $request->input('order');
        if (is_array($order) && !empty($order) && isset($order[0]['column']) && isset($order[0]['dir'])) {
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];
        }

        // Column name
        $columns = $request->input('columns');
        if (is_array($columns) && isset($columns[$columnIndex]['data'])) {
            $columnName = $columns[$columnIndex]['data'];
        }

        // Column mapping
        $columnMap = [
            'id' => 'id',
            'product_id' => 'product_id',
            'rating' => 'rating',
            'comment' => 'comment',
            'name' => 'name',
            'approved' => 'approved',
        ];

        $dbColumnName = $columnMap[$columnName] ?? 'id';

        // Search value
        $search = $request->input('search');
        if (is_array($search) && isset($search['value'])) {
            $searchValue = $search['value'];
        }

        // Build query
        $query = Avis::select([
            'avis.id',
            'avis.product_id',
            'avis.rating',
            'avis.comment',
            'avis.name',
            'avis.approved',
            'avis.created_at'
        ])
            ->with(['product:id,name'])
            ->orderByDesc('created_at');

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('comment', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('product', function ($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        // Apply product filter
        if ($product_id && $product_id !== 'all') {
            $query->where('product_id', $product_id);
        }

        // Get total records
        $totalRecords = $query->count();

        // Apply sorting and pagination
        $avis = $query->orderBy($dbColumnName, $columnSortOrder)
            ->offset($start)
            ->limit($rowPerPage)
            ->get();

        // Map data for DataTables
        $data_arr = $avis->map(function ($row) {
            return [
                'id' => $row->id,
                'date' => $row->created_at?->format('d-m-Y') ?? 'N/A',
                'product' => optional($row->product)->name ?? 'N/A',
                'rating' => $row->rating ?? 0,
                'comment' => $row->comment ?? 'N/A',
                'name' => $row->name ?? 'N/A',
                'approved' => $row->approved ? 'Approuvé' : 'En attente'
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data_arr,
        ]);
    }

}
