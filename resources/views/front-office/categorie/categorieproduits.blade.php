@extends('front-office.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                {{ $selectedCategory ? $selectedCategory->name : 'Notre Collection Exclusive' }}
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-6 sm:mb-8">
                Découvrez des pièces uniques pour sublimer votre intérieur
            </p>
            <div class="w-20 sm:w-24 h-1 bg-[#dfb54e] mx-auto rounded-full"></div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 sm:py-12 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Breadcrumb -->
            <div class="mb-6 sm:mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}"
                               class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-600 hover:text-[#dfb54e] transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Accueil
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <span
                                    class="ml-1 text-xs sm:text-sm font-medium text-gray-900 md:ml-2">{{ $selectedCategory ? $selectedCategory->name : 'Produits' }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Free Shipping Info -->
            <p class="text-green-600 mb-6 text-center">
                Livraison gratuite pour les commandes supérieures à {{ $freeShippingLimit }} DT
            </p>

            <!-- Mobile Filters -->
            <div x-data="{ mobileFiltersOpen: false }" class="mb-6 md:hidden">
                <button @click="mobileFiltersOpen = true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Filtres</span>
                </button>

                <!-- Off-canvas mobile filter -->
                <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     @keydown.escape.window="mobileFiltersOpen = false"
                     @click.away="mobileFiltersOpen = false"
                     class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-screen">
                        <div class="relative w-full max-w-xs bg-white shadow-xl ml-auto">
                            <div
                                class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-900">Filtres</h2>
                                <button @click="mobileFiltersOpen = false"
                                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#dfb54e]">
                                    <span class="sr-only">Fermer</span>
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-6 overflow-y-auto h-[calc(100vh-60px)]">
                                <!-- Categories list -->
                                <div class="mb-6">
                                    <h3 class="font-bold text-gray-800 mb-3">Catégories</h3>
                                    <ul class="space-y-2">
                                        <li>
                                            <a href="{{ route('produits.all') }}"
                                               class="flex items-center justify-between py-3 px-4 -mx-4 rounded-lg hover:bg-gray-50 transition-colors {{ !$selectedCategory ? 'bg-gray-100 font-semibold' : '' }}"
                                               @click="mobileFiltersOpen = false">
                                                <span class="text-gray-700">Tous les produits</span>
                                                <span
                                                    class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ \App\Models\Product::active()->count() }}</span>
                                            </a>
                                        </li>
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('categorie.produits', $category->slug) }}"
                                                   class="flex items-center justify-between py-3 px-4 -mx-4 rounded-lg hover:bg-gray-50 transition-colors {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-gray-100 font-semibold' : '' }}"
                                                   @click="mobileFiltersOpen = false">
                                                    <span class="text-gray-700">{{ $category->name }}</span>
                                                    <span
                                                        class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- CTA -->
                                <div
                                    class="md:hidden sticky bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-6 py-4">
                                    <button @click="mobileFiltersOpen = false"
                                            class="w-full bg-[#dfb54e] text-white py-3 rounded-lg font-medium hover:bg-[#cba640] transition">
                                        Appliquer les filtres
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Filters Sidebar - Desktop -->
                <aside class="hidden md:block w-full md:w-72 flex-shrink-0">
                    <div
                        class="bg-white p-4 sm:p-6 rounded-xl border border-gray-100 shadow-sm sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Filtres</h2>
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-3">Catégories</h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('produits.all') }}"
                                       class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors {{ !$selectedCategory ? 'bg-gray-100 font-semibold' : '' }}">
                                        <span class="text-gray-700">Tous les produits</span>
                                        <span
                                            class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ \App\Models\Product::active()->count() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('categorie.produits', $category->slug) }}"
                                           class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-gray-100 font-semibold' : '' }}">
                                            <span class="text-gray-700">{{ $category->name }}</span>
                                            <span
                                                class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    <div class="mb-4 sm:mb-6 flex justify-between items-center">
                        <p class="text-sm sm:text-base text-gray-600">
                            <span class="font-medium text-gray-900">{{ $products->total() }}</span> produits disponibles
                        </p>
                    </div>

                    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @forelse($products as $product)
                            <article
                                class="bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-lg border border-gray-100 hover:border-gray-200"
                                itemscope itemtype="http://schema.org/Product">
                                <div class="relative overflow-hidden aspect-square">
                                    <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                        <img src="{{ asset('storage/' . ($product->images[0] ?? 'default.jpg')) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                             loading="lazy"/>
                                    </a>
                                    @if($product->created_at->diffInDays(now()) < 10)
                                        <span
                                            class="absolute top-2 right-2 bg-[#228B22] text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Nouveau</span>
                                    @endif
                                    @if ($product->stock <= 5 && $product->stock > 0)
                                        <span
                                            class="absolute top-2 left-2 bg-white text-red-500 text-xs font-semibold px-2 py-1 rounded-lg uppercase shadow-sm">Stock faible</span>
                                    @elseif ($product->stock === 0)
                                        <span
                                            class="absolute top-2 left-2 bg-white text-gray-700 font-semibold text-xs px-2 py-1 rounded-lg uppercase shadow-sm">Épuisé</span>
                                    @endif
                                </div>

                                <div class="p-4">
                                    <h3
                                        class="text-base font-semibold text-gray-900 hover:text-[#dfb54e] transition-colors mb-1 line-clamp-2">
                                        <a href="{{ route('preview-article', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-2">
                                        {{ $product->description }}
                                    </p>
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.59-.921 1.89 0l1.59 3.18a1 0 00.89.67h3.43c.98 0 1.39 1.25.6 1.82l-2.78 2.02a1 0 00-.34 1.13l1.06 3.19c.3.91-.76 1.67-1.54 1.1l-2.78-2.02a1 0 00-1.16 0l-2.78 2.02c-.78.57-1.84-.19-1.54-1.1l1.06-3.19a1 0 00-.34-1.13L2.49 8.79c-.79-.57-.38-1.82.6-1.82h3.43a1 0 00.89-.67l1.59-3.18z">
                                            </path>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">4.8</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-black font-bold text-lg">
                                            {{ number_format($product->price, 2) }} DT
                                        </p>
                                        <button aria-label="Ajouter {{ $product->name }} au panier"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-image="{{ asset('storage/' . ($product->images[0] ?? 'default.jpg')) }}"
                                                data-stock="{{ $product->stock }}"
                                                class="flex items-center justify-center gap-1 bg-[#dfb54e] hover:bg-[#cba640] text-white p-2 rounded-lg transition-all duration-300"
                                                @if ($product->stock == 0) disabled @endif
                                                onclick="addToCart(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun produit trouvé</h3>
                                <p class="text-gray-500 mb-6">Essayez de modifier vos critères de recherche</p>
                                <a href="{{ route('produits.all') }}"
                                   class="inline-block px-6 py-2 bg-[#dfb54e] text-white rounded-lg hover:bg-[#cba640] transition font-medium">
                                    Réinitialiser les filtres
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($products->hasPages())
                        <div class="mt-8 sm:mt-12 border-t border-gray-100 pt-8">
                            {{ $products->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-12 sm:py-16 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 sm:px-6 max-w-4xl text-center">
            <div class="bg-white p-8 sm:p-10 rounded-lg">
                <h2 class="text-2xl sm:text-3xl font-semibold mb-3">Restez informé</h2>
                <p class="text-gray-600 mb-6 max-w-lg mx-auto">
                    Abonnez-vous à notre newsletter pour recevoir en exclusivité nos nouveautés.
                </p>
                <form class="newsletter-signup flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Votre adresse email"
                           class="flex-1 border border-gray-200 rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-0 focus:border-gray-300 shadow-sm">
                    <button type="submit"
                            class="bg-[#dfb54e] text-white px-4 py-3 rounded-lg font-medium hover:bg-[#cba640] transition shadow-sm">
                        S'abonner
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        article {
            transition: all 0.3s ease;
        }

        article:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .05);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pagination .active .page-link {
            background-color: #dfb54e;
            border-color: #dfb54e;
            color: #fff;
        }

        .pagination .page-link {
            color: #dfb54e;
            border: 1px solid #e5e7eb;
            padding: .5rem .75rem;
            border-radius: .375rem;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
        }

        .animate-fade-in-up {
            animation: fadeInUp .3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Align with off-canvas cart logic
        window.addToCart = function (button) {
            const loadingBar = document.getElementById('loadingBar');
            loadingBar?.classList.remove('hidden', 'scale-x-0');
            loadingBar?.classList.add('scale-x-100');
            button.disabled = true;
            const htmlBackup = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const product = {
                    id: Number(button.dataset.id),
                    name: button.dataset.name,
                    price: Number(button.dataset.price),
                    image: button.dataset.image,
                    stock: Number(button.dataset.stock),
                    quantity: 1
                };

                if (!product.id || !product.name || isNaN(product.price) || !product.image || isNaN(product.stock)) {
                    throw new Error('Données produit invalides');
                }
                if (product.stock === 0) {
                    throw new Error('Produit épuisé');
                }

                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                cart = cart.filter(i => i.id && i.name && !isNaN(i.price) && i.image && !isNaN(i.stock));
                const existingItem = cart.find(item => item.id === product.id);

                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity += 1;
                    } else {
                        showNotification('Stock maximum atteint', 'error');
                        return;
                    }
                } else {
                    cart.push(product);
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                updateCount();
                openCartOffcanvas();

                showNotification(`${product.name} ajouté au panier`, 'success');
            } catch (err) {
                showNotification(err.message, 'error');
                console.error(err);
            } finally {
                button.disabled = false;
                button.innerHTML = htmlBackup;
                loadingBar?.classList.remove('scale-x-100');
                loadingBar?.classList.add('scale-x-0');
                setTimeout(() => loadingBar?.classList.add('hidden'), 500);
            }
        };

        function showNotification(msg, type = 'success') {
            const notification = document.createElement('div');
            notification.className =
                `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-fade-in-up ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`;
            notification.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>${msg}</span>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function openCartOffcanvas() {
            const cartOffcanvas = document.getElementById('cartOffcanvas');
            const cartOverlay = document.getElementById('cartOverlay');
            if (cartOffcanvas && cartOverlay) {
                cartOffcanvas.classList.remove('translate-x-full');
                cartOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                renderCart();
            }
        }
    </script>
@endsection
