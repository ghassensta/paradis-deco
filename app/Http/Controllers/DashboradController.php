<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Statut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboradController extends Controller
{
    public function index()
    {
        /* ────────── 1. Produits ────────── */
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $productsThisMonth = Product::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        /* ────────── 2. Commandes payées ────────── */
        $paidOrders = Order::whereNotNull('paid_at');
        $nbPaidOrders = (clone $paidOrders)->count();
        $paidRevenue = (clone $paidOrders)->sum('subtotal_ht');

        /* ────────── 3. Top 5 produits vendus (quantité) ────────── */
        $topProducts = Product::query()
            ->select(
                'products.id',
                'products.name',
                'products.images',
                'products.price',                           // ➜ on veut le prix unitaire
                DB::raw('SUM(order_items.quantity) AS sold_qty')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('orders.paid_at')              // commandes payées
            ->groupBy('products.id', 'products.name', 'products.images', 'products.price')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->get();

        // dd($topProducts);
        /* ────────── 4. Injection dans la vue ────────── */
        return view('admin.dashborad', compact(
            'totalProducts',
            'activeProducts',
            'productsThisMonth',
            'nbPaidOrders',
            'paidRevenue',
            'topProducts'
        ));
    }
}



