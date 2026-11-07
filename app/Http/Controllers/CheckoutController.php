<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutConfirmMail;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Mail;

class CheckoutController extends Controller
{
    public const GOVERNORATES = [
        'Ariana', 'Béja', 'Ben Arous', 'Bizerte', 'Gabès', 'Gafsa', 'Jendouba', 'Kairouan',
        'Kasserine', 'Kébili', 'Le Kef', 'Mahdia', 'La Manouba', 'Médenine', 'Monastir', 'Nabeul',
        'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse', 'Tataouine', 'Tozeur', 'Tunis', 'Zaghouan',
    ];

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
        // « +216 20 123 456 », « 20.123.456 » et « 20123456 » désignent le même
        // client : le numéro est ramené à ses 8 chiffres avant la validation.
        if ($request->filled('phone')) {
            $phone = preg_replace('/[\s.\-()]/', '', (string) $request->input('phone'));
            $request->merge(['phone' => preg_replace('/^(\+|00)216/', '', $phone)]);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => ['required', 'regex:/^[2-9]\d{7}$/'],
            'address' => 'required|string|max:255',
            'city' => ['required', Rule::in(self::GOVERNORATES)],
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer',
            'cart.*.name' => 'required|string',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.variant_id' => 'nullable|integer',
            'checkout_key' => 'nullable|string|max:64',
        ], [
            'full_name.required' => 'Veuillez indiquer votre nom et prénom.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'phone.required' => 'Veuillez indiquer votre numéro de téléphone.',
            'phone.regex' => 'Le numéro de téléphone doit comporter 8 chiffres (ex. : 20 123 456).',
            'address.required' => 'Veuillez indiquer votre adresse de livraison.',
            'city.required' => 'Veuillez choisir votre gouvernorat.',
            'city.in' => 'Veuillez choisir un gouvernorat dans la liste.',
            'cart.required' => 'Votre panier est vide.',
        ]);

        /* Tentative déjà aboutie (double clic, réponse perdue, second onglet) :
           on rend la commande existante, avant tout contrôle de stock. */
        $checkoutKey = $validated['checkout_key'] ?? null;

        if ($existing = $this->orderForKey($checkoutKey)) {
            return $this->orderConfirmed($existing);
        }

        $cart = $this->resolveCart($validated['cart']);
        unset($validated['cart'], $validated['checkout_key']);   // reste = infos client

        /* ──────────────── 2. Transaction BDD ──────────────── */
        DB::beginTransaction();
        try {
            /* 2.1 Client */
            // Le client garde ses coordonnées les plus récentes ; la commande,
            // elle, conserve l'adresse saisie pour cette livraison.
            $client = Client::updateOrCreate(
                ['phone' => $validated['phone']],
                array_filter([
                    'name' => $validated['full_name'],
                    'email' => $validated['email'] ?? null,
                    'adresse' => $validated['address'],
                    'city' => $validated['city'],
                ])
            );

            /* 2.2 Montants panier */
            $subTotal = collect($cart)
                ->sum(fn($item) => $item['price'] * $item['quantity']);   // HT

            /* 2.3 Config boutique */
            $config = Configuration::first();            // ou config('shop.…')
            $shippingCostFixed = (float) ($config->shipping_cost ?? 0);
            $freeShippingThreshold = (float) ($config->free_shipping_threshold ?? 0);

            /* 2.4 Frais de port — un seuil à 0 signifie « pas de livraison
               offerte », surtout pas « offerte dès le premier dinar ». */
            $shippingCost = $freeShippingThreshold > 0 && $subTotal >= $freeShippingThreshold
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
                'checkout_key' => $checkoutKey,
                'address' => $validated['address'],
                'city' => $validated['city'],
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
                    'product_variant_id' => $item['variant_id'],
                    'variant_label' => $item['variant_label'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            $this->sendConfirmation($order, $validated['email'] ?? null);

            return $this->orderConfirmed($order);

        } catch (UniqueConstraintViolationException $e) {
            DB::rollBack();

            // Envoi simultané : l'autre requête a créé la commande juste avant.
            if ($existing = $this->orderForKey($checkoutKey)) {
                return $this->orderConfirmed($existing);
            }

            report($e);

            return response()->json([
                'message' => 'Erreur lors de l’enregistrement de la commande.',
            ], 500);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Erreur lors de l’enregistrement de la commande.',
            ], 500);
        }
    }

    private function orderForKey(?string $checkoutKey): ?Order
    {
        return $checkoutKey ? Order::withTrashed()->firstWhere('checkout_key', $checkoutKey) : null;
    }

    private function orderConfirmed(Order $order)
    {
        return response()->json([
            'message' => 'Commande enregistrée.',
            // Renvoyé pour que le front puisse identifier la commande et
            // n'envoyer l'évènement Purchase qu'une seule fois.
            'order_reference' => $order->numero_commande,
        ]);
    }

    /**
     * La commande est déjà validée : une panne d'e-mail ne doit pas renvoyer
     * une erreur au client, qui recommanderait alors une seconde fois.
     */
    private function sendConfirmation(Order $order, ?string $email): void
    {
        try {
            if (filled($email)) {
                Mail::to($email)->queue(new CheckoutConfirmMail($order));
            }

            // À l’admin. env() renvoyait null (clé absente du .env, et vidée
            // par config:cache) : le job échouait silencieusement en file.
            if ($adminRecipient = config('mail.admin_to')) {
                Mail::to($adminRecipient)->queue(new CheckoutConfirmMail($order));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Le panier vit dans le localStorage du client : seuls l'article et la
     * quantité en sont repris, le prix et la variante viennent de la base.
     */
    private function resolveCart(array $cart): array
    {
        $products = Product::with('activeVariants')
            ->whereIn('id', array_column($cart, 'id'))
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (array $item, int $index) use ($products) {
            $product = $products->get($item['id']);

            if (! $product) {
                throw ValidationException::withMessages([
                    "cart.$index.id" => "« {$item['name']} » n'est plus disponible.",
                ]);
            }

            if (! $product->has_variants) {
                return [
                    ...$item,
                    'price' => (float) $product->price,
                    'variant_id' => null,
                    'variant_label' => null,
                ];
            }

            $variant = $product->activeVariants->firstWhere('id', (int) ($item['variant_id'] ?? 0));

            if (! $variant) {
                throw ValidationException::withMessages([
                    "cart.$index.variant_id" => "Veuillez choisir une option pour « {$product->name} ».",
                ]);
            }

            if ($item['quantity'] > $variant->stock) {
                throw ValidationException::withMessages([
                    "cart.$index.quantity" => "Stock insuffisant pour « {$product->name} » ({$variant->label}).",
                ]);
            }

            return [
                ...$item,
                'price' => (float) $variant->price,
                'variant_id' => $variant->id,
                'variant_label' => $variant->label,
            ];
        })->all();
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
