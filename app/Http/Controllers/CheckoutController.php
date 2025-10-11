<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutConfirmMail;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('front-office.checkout.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrder(Request $request)
    {
        /* ─────────────────── 1. Validation ─────────────────── */
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer',
            'cart.*.name' => 'required|string',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $cart = $validated['cart'];
        unset($validated['cart']);   // reste = infos client

        /* ──────────────── 2. Transaction BDD ──────────────── */
        DB::beginTransaction();
        try {
            /* 2.1 Client */
            $client = Client::firstOrCreate(
                ['phone' => $validated['phone']],
                [
                    'name' => $validated['full_name'],
                    'email' => $validated['email'] ?? null,
                    'adresse' => $validated['address'],
                ]
            );

            /* 2.2 Montants panier */
            $subTotal = collect($cart)
                ->sum(fn($item) => $item['price'] * $item['quantity']);   // HT

            /* 2.3 Config boutique */
            $config = Configuration::first();            // ou config('shop.…')
            $shippingCostFixed = (float) ($config->shipping_cost ?? 0);
            $freeShippingThreshold = (float) ($config->free_shipping_threshold ?? 0);

            /* 2.4 Frais de port */
            $shippingCost = $subTotal >= $freeShippingThreshold
                ? 0
                : $shippingCostFixed;

            /* 2.5 TVA (19 %) sur HT + port */
            $taxRate = 0.19;
            $taxBase = $subTotal + $shippingCost;
            $taxAmount = round($taxBase * $taxRate, 2);

            /* 2.6 Total TTC */
            $grandTotal = round($taxBase + $taxAmount, 2);

            /* 2.7 Création de la commande */
            $order = Order::create([
                'numero_commande' => 'CMD-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'statut_id' => 4,            // « en cours »
                'subtotal_ht' => $subTotal,
                'shipping_cost' => $shippingCost,
                'tax_rate' => $taxRate,     // pour mémoire
                'tax_tva' => $taxAmount,
                'total_ttc' => $grandTotal,
                'shipped_at' => null,
                'paid_at' => null,
            ]);

            /* 2.8 Articles */
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();
            if (isset($validated['email']) && $validated['email'] !== null && $validated['email'] !== '') {
                Mail::to($validated['email'])->queue(new CheckoutConfirmMail($order));
            }

            // À l’admin
            Mail::to(env('EMAIL_ADMIN'))
                ->queue(new CheckoutConfirmMail($order));

            return response()->json(['message' => 'Commande enregistrée.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Erreur lors de l’enregistrement de la commande.',
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
