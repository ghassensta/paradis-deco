{{-- resources/views/front-office/checkout.blade.php --}}
@extends('front-office.layouts.app')

@php
    /*  Paramètres livraison  */
    $shippingCost = (float) ($config->shipping_cost ?? 7); // 7 DT par défaut
    $freeShippingLimit = (float) ($config->free_shipping_limit ?? 150);
@endphp

@section('title', 'Finaliser votre commande | Paradis Déco Tunisie')

@section('meta')
    <meta name="description"
        content="Finalisez votre commande sur Paradis Déco Tunisie. Livraison rapide, paiement à la livraison, déco de qualité garantie.">
    <meta property="og:title" content="Finaliser votre commande | Paradis Déco">
    <meta property="og:description"
        content="Achetez facilement vos articles de décoration. Paiement à la livraison partout en Tunisie.">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta name="robots" content="noindex, nofollow">
@endsection



@section('content')
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-6 max-w-7xl">
            <h1 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Finaliser Votre Commande
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- ================= FORMULAIRE ================= -->
                <!-- ================= FORMULAIRE ================= -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <form id="checkoutForm" action="/order/submit" method="POST" class="space-y-6">
                        @csrf

                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            Informations de Livraison
                        </h2>

                        <!-- Prénom / Nom -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">Nom & Prénom *</label>
                            <input type="text" id="first_name" name="full_name" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                              focus:ring-[#dfb54e] focus:border-[#dfb54e] sm:text-sm p-2"
                                placeholder="Nom & Prénom">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (optionnel)</label>
                            <input type="email" id="email" name="email"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                  focus:ring-[#dfb54e] focus:border-[#dfb54e] sm:text-sm p-2"
                                placeholder="votre@email.com">
                        </div>


                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                            <input type="tel" id="phone" name="phone" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                          focus:ring-[#dfb54e] focus:border-[#dfb54e] sm:text-sm p-2"
                                placeholder="+216 12 345 678">
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Adresse *</label>
                            <input type="text" id="address" name="address" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                          focus:ring-[#dfb54e] focus:border-[#dfb54e] sm:text-sm p-2"
                                placeholder="123 Rue de la Déco">
                        </div>
                        <button type="submit" id="submitOrder"
                            class="w-full bg-[#dfb54e] hover:bg-[#cfa640] text-white py-3 rounded-lg
                       font-semibold text-lg transition-colors duration-300 shadow-md
                       hover:shadow-lg flex items-center justify-center focus:outline-none
                       focus:ring-2 focus:ring-[#dfb54e]">
                            <span>Passer la Commande</span>
                            <svg id="submitSpinner" class="hidden animate-spin h-5 w-5 text-white ml-2"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </form>
                </div>


                <!-- ================== RÉSUMÉ =================== -->
                <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-20">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        Résumé de la Commande
                    </h2>

                    <div id="orderSummary" class="space-y-4">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Votre panier est vide
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-4 space-y-2 text-gray-800">
                        <div class="flex justify-between">
                            <span>Sous-total</span>
                            <span id="orderSubtotal">0 DT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Frais de livraison</span>
                            <span id="orderShipping">0 DT</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span id="orderTotal">0 DT</span>
                        </div>
                    </div>

                    <a href="/panier"
                        class="block mt-4 text-[#dfb54e] hover:text-[#ae8d3c] text-center font-medium hover:underline">
                        Modifier le panier
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

{{-- ========================= STYLES ======================== --}}
@section('css')
    <style>
        #checkoutForm input {
            transition: border-color .3s, box-shadow .3s;
        }

        #checkoutForm input:focus {
            border-color: #dfb54e;
            box-shadow: 0 0 0 3px rgba(223, 181, 78, .15);
        }

        #submitOrder.loading {
            background: #e3c786;
            cursor: wait;
        }

        #submitOrder.loading #submitSpinner {
            display: inline-block;
        }

        #orderSummary .flex {
            transition: background-color .2s;
        }

        #orderSummary .flex:hover {
            background: #f9fafb;
        }

        #orderSummary img {
            border: 1px solid #e5e7eb;
        }

        @media(max-width:1024px) {
            .sticky {
                position: static;
            }
        }
    </style>
@endsection

{{-- ========================= SCRIPTS ======================= --}}
@section('js')
    <script>
        /* Constantes livraison */
        const SHIPPING_const = {{ json_encode($shippingCost, JSON_NUMERIC_CHECK) }};
        const FREE_SHIPPING_Amount = {{ json_encode($freeShippingLimit, JSON_NUMERIC_CHECK) }};
        console.log("SHIPPING_FEE",SHIPPING_const);
        console.log("FREE_SHIPPING_MIN",FREE_SHIPPING_Amount);
        /* Helpers LocalStorage */
        const STORAGE_KEY = 'cart';
        const sanitize = c => c.filter(i => i.id && i.name && i.image && !isNaN(i.price));
        const getCart = () => JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        const showNotification = (msg, type = 'success') => {
            const n = document.createElement('div');
            n.className = `fixed top-10 right-4 z-50 px-4 py-2 rounded-lg shadow-lg
                ${type==='success'?'bg-green-500':'bg-red-500'} text-white`;
            n.textContent = msg;
            document.body.appendChild(n);
            setTimeout(() => n.remove(), 3000);
        };

        /* Mise à jour résumé */
        function updateOrderSummary() {
            const summary = document.getElementById('orderSummary');
            const subT = document.getElementById('orderSubtotal');
            const shipT = document.getElementById('orderShipping');
            const totT = document.getElementById('orderTotal');

            const cart = sanitize(getCart());
            if (cart.length === 0) {
                summary.innerHTML = '<div class="text-center py-4 text-gray-500 text-sm">Votre panier est vide</div>';
                subT.textContent = '0 DT';
                shipT.textContent = '0 DT';
                totT.textContent = '0 DT';
                return;
            }

            let subtotal = 0;
            summary.innerHTML = cart.map(i => {
                const line = i.price * i.quantity;
                subtotal += line;
                return `<div class="flex items-center py-2">
                    <img src="${i.image}" alt="${i.name}"
                         class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                    <div class="ml-4 flex-1">
                        <h4 class="font-medium text-gray-800 text-sm">${i.name}</h4>
                        <p class="text-gray-500 text-xs">${i.quantity} × ${i.price.toFixed(2)} DT</p>
                    </div>
                    <p class="text-gray-600 font-semibold text-sm">${line.toFixed(2)} DT</p>
                </div>`;
            }).join('');

            const shipping = subtotal >= FREE_SHIPPING_Amount ? 0 : SHIPPING_const;
            const total = subtotal + shipping;

            subT.textContent = `${subtotal.toFixed(2)} DT`;
            shipT.textContent = shipping === 0 ? 'Offert' : `${shipping.toFixed(2)} DT`;
            totT.textContent = `${total.toFixed(2)} DT`;
        }

        /* Soumission Ajax */
        document.addEventListener('DOMContentLoaded', () => {
            updateOrderSummary();

            const form = document.getElementById('checkoutForm');
            const btn = document.getElementById('submitOrder');
            const spinner = document.getElementById('submitSpinner');

            form.addEventListener('submit', async e => {
                e.preventDefault();
                let invalid = false;
                form.querySelectorAll('[required]').forEach(f => {
                    f.classList.toggle('border-red-500', !f.value.trim());
                    if (!f.value.trim()) invalid = true;
                });
                if (invalid) {
                    showNotification('Veuillez remplir tous les champs.', 'error');
                    return;
                }

                const payload = Object.fromEntries(new FormData(form));
                const cart = sanitize(getCart());
                if (cart.length === 0) {
                    showNotification('Votre panier est vide.', 'error');
                    return;
                }

                btn.classList.add('loading');
                spinner.classList.remove('hidden');
                try {
                    const res = await fetch('/order/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            ...payload,
                            cart
                        })
                    });
                    if (res.ok) {
                        const data = await res.json();
                        localStorage.removeItem(STORAGE_KEY);
                        updateOrderSummary();
                        showNotification(data.message || 'Commande confirmée !');
                        data.redirect && (window.location.href = data.redirect);
                    } else if (res.status === 422) {
                        const errs = await res.json();
                        showNotification(Object.values(errs.errors).flat().join('\n'), 'error');
                    } else {
                        showNotification('Erreur serveur.', 'error');
                    }
                } catch (err) {
                    showNotification('Connexion impossible.', 'error');
                    console.log(err);
                } finally {
                    btn.classList.remove('loading');
                    spinner.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
