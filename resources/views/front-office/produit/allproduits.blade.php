@extends('front-office.layouts.app')

{{-- ============  MÉTADONNÉES – PAGE LISTE PRODUITS  ============ --}}
@section('title', 'Tous nos produits | Paradis Déco')

@section('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport"           content="width=device-width, initial-scale=1">

<meta name="description"
      content="Paradis Déco : découvrez notre collection complète de meubles, luminaires, tapis et accessoires pour sublimer votre intérieur en Tunisie.">
<meta name="keywords"
      content="boutique déco en ligne Tunisie, décoration intérieure, meubles tunisiens, luminaires, tapis artisanaux, accessoires maison, artisanat tunisien, ameublement moderne, cadeaux maison">
<meta name="robots"
      content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="theme-color"        content="#FDD835">
<meta name="author"             content="Paradis Déco">
<meta name="publisher"          content="Paradis Déco">

<link rel="canonical"           href="{{ url()->current() }}">
<link rel="alternate"           href="{{ url()->current() }}" hreflang="fr-tn">
<link rel="alternate"           href="{{ url()->current() }}" hreflang="x-default">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Liste des produits - Paradis Déco",
  "description": "Découvrez tous nos produits disponibles sur Paradis Déco.",
  "url": "{{ url()->current() }}",
  "numberOfItems": {{ $products->total() }},
  "itemListElement": [
    @foreach($products as $key => $prod)
      {
        "@type": "ListItem",
        "position": {{ $loop->index + 1 }},
        "url": "{{ route('preview-article', $prod->slug) }}",
        "item": {
          "@type": "Product",
          "name": "{{ $prod->name }}",
          "image": "{{ asset('storage/' . $prod->image_avant) }}",
          "description": "{{ Str::limit(strip_tags($prod->description), 120) }}",
          "offers": {
            "@type": "Offer",
            "priceCurrency": "TND",
            "price": "{{ number_format($prod->price, 2, '.', '') }}",
            "availability": "https://schema.org/{{ $prod->stock > 0 ? 'InStock' : 'OutOfStock' }}",
            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
            "url": "{{ route('preview-article', $prod->slug) }}"
          }
        }
      }@if(!$loop->last),@endif
    @endforeach
  ]
}
</script>

@endsection

@section('content')
    <!-- Hero Section - Amélioré avec un dégradé subtil -->
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                Notre Collection Exclusive
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-6 sm:mb-8">
                Découvrez des pièces uniques pour sublimer votre intérieur
            </p>
            <div class="w-20 sm:w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
    </section>

    <!-- Main Content - Structure améliorée -->
    <section class="py-8 sm:py-12 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Breadcrumb - Style amélioré -->
            <div class="mb-6 sm:mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="/" class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-600 hover:text-primary transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Accueil
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-xs sm:text-sm font-medium text-gray-900 md:ml-2">Produits</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Mobile Filters - Amélioré avec animation -->
          <!-- Mobile Filters - Version améliorée -->
<div x-data="{ mobileFiltersOpen: false }" class="mb-6 md:hidden">
    <button @click="mobileFiltersOpen = true" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">Filtres</span>
    </button>

    <!-- Mobile Filters Panel - Version améliorée -->
    <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
        @keydown.escape.window="mobileFiltersOpen = false"
        @click.away="mobileFiltersOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen">
            <div class="relative w-full max-w-xs bg-white shadow-xl ml-auto">
                <!-- Header avec bouton de fermeture amélioré -->
                <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Filtres</h2>
                    <button @click="mobileFiltersOpen = false" class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Contenu scrollable -->
                <div class="p-6 overflow-y-auto h-[calc(100vh-60px)]">
                    <!-- Overlay de fermeture au clic à gauche (sur les écrans larges) -->
                    <div class="hidden md:block fixed inset-0 left-0 w-[calc(100%-20rem)] bg-black bg-opacity-50 z-40"
                         @click="mobileFiltersOpen = false"
                         x-show="mobileFiltersOpen"></div>

                    <!-- Catégories - Version améliorée -->
                    <div class="mb-6">
                        <h3 class="font-bold text-gray-800 mb-3 flex items-center justify-between">
                            <span class="text-lg">Catégories</span>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h3>
                        <ul class="space-y-2">
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('categorie.produits', $category->slug) }}"
                                   class="flex items-center justify-between py-3 px-4 -mx-4 rounded-lg hover:bg-gray-50 transition-colors"
                                   @click="mobileFiltersOpen = false">
                                    <span class="text-gray-700">{{ $category->name }}</span>
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Bouton Appliquer visible en bas sur mobile -->
                    <div class="md:hidden sticky bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-6 py-4">
                        <button @click="mobileFiltersOpen = false"
                                class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark transition">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Filters Sidebar - Desktop - Style amélioré -->
                <aside class="hidden md:block w-full md:w-72 flex-shrink-0">
                    <div class="bg-white p-4 sm:p-6 rounded-xl border border-gray-100 shadow-sm sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Filtres</h2>

                        <!-- Categories - Style amélioré -->
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center justify-between cursor-pointer">
                                <span>Catégories</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </h3>
                            <ul class="space-y-2">

                                @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('categorie.produits', $category->slug) }}" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <span class="text-gray-700">{{ $category->name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid - Structure améliorée -->
                <div class="flex-1">
                    <!-- Products Count - Style amélioré -->
                    <div class="mb-4 sm:mb-6 flex justify-between items-center">
                        <p class="text-sm sm:text-base text-gray-600">
                            <span class="font-medium text-gray-900">{{ $products->total() }}</span> produits disponibles
                        </p>
                    </div>

                    <!-- Products List - Design amélioré -->
                    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @forelse($products as $product)
                        <article class="bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-lg border border-gray-100 hover:border-gray-200" itemscope itemtype="http://schema.org/Product">
                            <!-- Image Section - Style amélioré -->
                            <div class="relative overflow-hidden aspect-square">
                                <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                    <img src="{{ asset('storage/' . $product->image_avant ?? $product->images[0]) }}" alt="{{ $product->name }} | {{ $config->site_name }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy" itemprop="image" />
                                </a>
                                <!-- Badges - Style amélioré -->
                                @if($product->created_at->diffInDays(now()) < 30)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-[#228B22] text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Nouveau</span>
                                </div>
                                @endif
                                @if ($product->stock <= 5 && $product->stock > 0)
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Stock faible</span>
                                    </div>
                                @elseif ($product->stock == 0)
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-gray-600 text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Épuisé</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Section - Style amélioré -->
                            <div class="p-4">
                                <div class="mb-3">
                                    <h3 class="text-base font-semibold text-gray-900 hover:text-primary transition-colors mb-1 line-clamp-2" itemprop="name">
                                        <a href="{{ route('preview-article', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="text-gray-500 text-xs sm:text-sm line-clamp-2 mb-2">{{ $product->description }}</p>

                                    <!-- Rating - Style amélioré -->
                                    <div class="flex items-center mb-2" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-gray-600" itemprop="ratingValue">4.8</span>
                                            <meta itemprop="reviewCount" content="120">
                                        </div>
                                    </div>
                                </div>

                                <!-- Price and Action - Style amélioré -->
                                <div class="flex justify-between items-center">
                                   <p class="text-black font-bold text-lg"
                                            itemprop="offers"
                                            itemscope
                                            itemtype="https://schema.org/Offer">

                                                <span itemprop="price">{{ number_format($product->price, 2) }}</span> DT

                                                <meta itemprop="priceCurrency" content="TND">

                                                {{-- disponibilité --}}
                                                <link itemprop="availability"
                                                    href="https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}">

                                                {{-- date de validité du prix --}}
                                                <meta itemprop="priceValidUntil" content="{{ now()->addYear()->format('Y-m-d') }}">

                                                {{-- URL du produit --}}
                                                <link itemprop="url" href="{{ route('preview-article', $product->slug) }}">
                                            </p>

                                    <button aria-label="Ajouter {{ $product->name }} au panier"
                                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-image="{{ asset('storage/' . $product->images[0]) }}"
                                        data-stock="{{ $product->stock }}"
                                        class="cursor-pointer flex items-center justify-center gap-1 bg-black hover:bg-black-dark text-white p-2 rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 hover:shadow-md"
                                        @if ($product->stock == 0) disabled @endif onclick="addToCart(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <div class="max-w-md mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun produit trouvé</h3>
                                <p class="text-gray-500 mb-6">Essayez de modifier vos critères de recherche</p>
                                <a href="{{ route('produits.index') }}" class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition font-medium">
                                    Réinitialiser les filtres
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination - Style amélioré -->
                    @if($products->hasPages())
                    <div class="mt-8 sm:mt-12 border-t border-gray-100 pt-8">
                        {{ $products->links('vendor.pagination.tailwind') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter - Design amélioré -->
    <section class="py-12 sm:py-16 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 sm:px-6 max-w-4xl text-center">
            <div class="bg-white p-8 sm:p-10 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Restez informé</h2>
                <p class="text-gray-600 mb-6 max-w-lg mx-auto">
                    Abonnez-vous à notre newsletter pour recevoir en exclusivité nos nouveautés et offres spéciales.
                </p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Votre email" class="flex-1 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm">
                    <button type="submit" class="bg-black text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition text-sm shadow-sm hover:shadow-md">
                        S'abonner
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('css')
<style>
    /* Product hover effect - Amélioré */
    article {
        transition: all 0.3s ease;
    }
    article:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    /* Badges - Style amélioré */
    [class^="bg-"]:not(.bg-white):not(.bg-gray-50) {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Line clamping for text */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination active state - Amélioré */
    .pagination .active .page-link {
        background-color: #000;
        border-color: #000;
        color: white;
        font-weight: 500;
    }
    .pagination .page-link {
        color: #000;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
    .pagination .page-link:hover {
        background-color: #f3f4f6;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function addToCart(button) {
        const product = {
            id: button.dataset.id,
            name: button.dataset.name,
            price: button.dataset.price,
            image: button.dataset.image,
            quantity: 1
        };

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(product);
        }

        localStorage.setItem('cart', JSON.stringify(cart));

        // Notification améliorée
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-fade-in-up';
        notification.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>${product.name} ajouté au panier</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>
@endsection
