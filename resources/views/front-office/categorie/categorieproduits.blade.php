@extends('front-office.layouts.app')

{{-- Meta Title de la catégorie --}}
@section('title', $selectedCategory->meta_title ?: $selectedCategory->name)

@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta name="theme-color" content="#FDD835">

    {{-- Meta Description --}}
    <meta name="description"
          content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">

    {{-- Meta Keywords --}}
    <meta name="keywords" content="{{ $selectedCategory->meta_keywords ?: $selectedCategory->name . ', meubles, décoration' }}">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- Open Graph --}}
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $selectedCategory->meta_title ?: $selectedCategory->name }}">
    <meta property="og:description" content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $selectedCategory->image ? asset('storage/' . $selectedCategory->image) : asset('images/default-og.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $selectedCategory->meta_title ?: $selectedCategory->name }}">
    <meta name="twitter:description" content="{{ $selectedCategory->meta_description ?: Str::limit(strip_tags($selectedCategory->description ?? ''), 155) }}">
    <meta name="twitter:image" content="{{ $selectedCategory->image ? asset('storage/' . $selectedCategory->image) : asset('images/default-og.jpg') }}">
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                {{ $selectedCategory->name ?? 'Notre Collection Exclusive' }}
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-6 sm:mb-8">
                {{ $selectedCategory->meta_description ?: 'Découvrez des pièces uniques pour sublimer votre intérieur' }}
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
                            <a href="{{ route('home') }}" class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-600 hover:text-[#dfb54e] transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Accueil
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-xs sm:text-sm font-medium text-gray-900 md:ml-2">{{ $selectedCategory->name ?? 'Produits' }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Free Shipping Info -->
            <p class="text-green-600 mb-6 text-center">
                Livraison gratuite pour les commandes supérieures à {{ $freeShippingLimit ?? 200 }} DT
            </p>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar Desktop -->
                <aside class="hidden md:block w-full md:w-72 flex-shrink-0">
                    <div class="bg-white p-4 sm:p-6 rounded-xl border border-gray-100 shadow-sm sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Filtres</h2>
                        <div class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-3">Catégories</h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('allproduits') }}"
                                       class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors {{ !$selectedCategory ? 'bg-gray-100 font-semibold' : '' }}">
                                        <span class="text-gray-700">Tous les produits</span>
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $totalProducts ?? \App\Models\Product::active()->count() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('categorie.produits', $category->slug) }}"
                                           class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-gray-100 font-semibold' : '' }}">
                                            <span class="text-gray-700">{{ $category->name }}</span>
                                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
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
                            @if($selectedCategory)
                                <span class="text-xs ml-1">dans « {{ $selectedCategory->name }} »</span>
                            @endif
                        </p>
                    </div>

                    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @forelse($products as $product)
                            <article class="bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-lg border border-gray-100 hover:border-gray-200">
                                <div class="relative overflow-hidden aspect-square">
                                    @php
                                        $catImagePath = $product->images[0] ?? $product->image_avant ?? null;
                                        $catImageDir  = $catImagePath ? trim(dirname($catImagePath), '/.') : null;
                                        $catImageFile = $catImagePath ? pathinfo($catImagePath, PATHINFO_FILENAME) : null;
                                        $catImageBase = ($catImageDir && $catImageDir !== '.') ? $catImageDir . '/' . $catImageFile : $catImageFile;
                                    @endphp
                                    <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                        @if($catImageBase)
                                            <picture>
                                                <source type="image/webp"
                                                        srcset="{{ asset('storage/' . $catImageBase . '-320.webp') }} 320w,
                                                                {{ asset('storage/' . $catImageBase . '-640.webp') }} 640w,
                                                                {{ asset('storage/' . $catImageBase . '-960.webp') }} 960w"
                                                        sizes="(min-width: 1280px) 25vw,
                                                               (min-width: 1024px) 33vw,
                                                               (min-width: 640px) 50vw,
                                                               100vw">
                                                <img src="{{ asset('storage/' . $catImageBase . '-640.jpg') }}"
                                                     alt="{{ $product->name }}"
                                                     srcset="{{ asset('storage/' . $catImageBase . '-320.jpg') }} 320w,
                                                             {{ asset('storage/' . $catImageBase . '-640.jpg') }} 640w,
                                                             {{ asset('storage/' . $catImageBase . '-960.jpg') }} 960w"
                                                     sizes="(min-width: 1280px) 25vw,
                                                            (min-width: 1024px) 33vw,
                                                            (min-width: 640px) 50vw,
                                                            100vw"
                                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                     loading="lazy" width="500" height="500" decoding="async"/>
                                            </picture>
                                        @else
                                            <img src="{{ asset('storage/' . ($product->images[0] ?? $product->image_avant ?? 'default.jpg')) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover transition-transform.duration-500 group-hover:scale-105"
                                                 loading="lazy" width="500" height="500" decoding="async"/>
                                        @endif
                                    </a>
                                    @if($product->created_at->diffInDays(now()) < 10)
                                        <span class="absolute top-2 right-2 bg-[#228B22] text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Nouveau</span>
                                    @endif
                                    @if ($product->stock <= 5 && $product->stock > 0)
                                        <span class="absolute top-2 left-2 bg-white text-red-500 text-xs font-semibold px-2 py-1 rounded-lg uppercase shadow-sm">Stock faible</span>
                                    @elseif ($product->stock === 0)
                                        <span class="absolute top-2 left-2 bg-white text-gray-700 font-semibold text-xs px-2 py-1 rounded-lg uppercase shadow-sm">Épuisé</span>
                                    @endif
                                </div>

                                <div class="p-4">
                                    <h3 class="text-base font-semibold text-gray-900 hover:text-[#dfb54e] transition-colors mb-1 line-clamp-2">
                                        <a href="{{ route('preview-article', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="text-gray-600 text-xs sm:text-sm line-clamp-2 mb-2">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.59-.921 1.89 0l1.59 3.18a1 0 00.89.67h3.43c.98 0 1.39 1.25.6 1.82l-2.78 2.02a1 0 00-.34 1.13l1.06 3.19c.3.91-.76 1.67-1.54 1.1l-2.78-2.02a1 0 00-1.16 0l-2.78 2.02c-.78.57-1.84-.19-1.54-1.1l1.06-3.19a1 0 00-.34-1.13L2.49 8.79c-.79-.57-.38-1.82.6-1.82h3.43a1 0 00.89-.67l1.59-3.18z"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ $product->avis()->where('approved', true)->count() > 0
                                                ? number_format($product->avis()->where('approved', true)->avg('rating'), 1)
                                                : '5.0' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-black font-bold text-lg">
                                            {{ number_format($product->price, 2) }} DT
                                        </p>
                                        <button aria-label="Ajouter {{ $product->name }} au panier"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-image="{{ asset('storage/' . ($product->images[0] ?? $product->image_avant ?? 'default.jpg')) }}"
                                                data-stock="{{ $product->stock }}"
                                                class="flex items-center justify-center gap-1 bg-[#dfb54e] hover:bg-[#cba640] text-white p-2 rounded-lg transition-all duration-300 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $product->stock == 0 ? 'disabled' : '' }}
                                                onclick="addToCart(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun produit trouvé</h3>
                                <p class="text-gray-500 mb-6">
                                    Aucun produit disponible dans « {{ $selectedCategory->name ?? 'cette catégorie' }} ».
                                </p>
                                <a href="{{ route('allproduits') }}"
                                   class="inline-block px-6 py-2 bg-[#dfb54e] text-white rounded-lg hover:bg-[#cba640] transition font-medium">
                                    Voir tous les produits
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
@endsection

@section('css')
<style>
    article { transition: all 0.3s ease; }
    article:hover { transform: translateY(-3px); box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pagination .page-item.active .page-link { background-color: #dfb54e !important; border-color: #dfb54e !important; color: #fff !important; }
    .pagination .page-link { color: #dfb54e; border: 1px solid #e5e7eb; padding: .5rem .75rem; border-radius: .375rem; margin: 0 2px; }
    .pagination .page-link:hover { background-color: #f3f4f6; border-color: #dfb54e; color: #dfb54e; }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    window.addToCart = function (button) {
        if(button.disabled) return;
        let product = {
            id: Number(button.dataset.id),
            name: button.dataset.name,
            price: Number(button.dataset.price),
            image: button.dataset.image,
            stock: Number(button.dataset.stock),
            quantity: 1
        };
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        let existing = cart.find(i => i.id === product.id);
        if(existing) {
            if(existing.quantity < product.stock) existing.quantity +=1;
            else { alert('Stock maximum atteint'); return; }
        } else cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${product.name} ajouté au panier !`);
    };
</script>
@endsection
