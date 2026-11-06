@extends('front-office.layouts.app')


@php
    // Page 1 = landing SEO ; pages suivantes : self-canonical + noindex,follow
    // pour éviter le duplicate, les produits restant crawlables (follow).
    $currentPage = $products->currentPage();
    $isFirstPage = $currentPage === 1;
    $canonical = $isFirstPage
        ? $selectedCategory->url
        : $selectedCategory->url . '?page=' . $currentPage;
    $seoTitle = $selectedCategory->seo_title;
    $seoDescription = $selectedCategory->seo_description;
    $ogImage = $selectedCategory->image
        ? asset('storage/' . $selectedCategory->image)
        : asset('images/default-og.jpg');
@endphp

@section('description', $seoDescription)
@section('title', $seoTitle)

@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="{{ $isFirstPage ? 'index,follow' : 'noindex,follow' }},max-snippet:-1,max-image-preview:large">
    <meta name="theme-color" content="#FDD835">

    @if ($selectedCategory->meta_keywords)
        <meta name="keywords" content="{{ $selectedCategory->meta_keywords }}">
    @endif

    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" href="{{ $selectedCategory->url }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ $selectedCategory->url }}" hreflang="x-default">

    {{-- Open Graph --}}
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    {{-- Fil d'Ariane structuré --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($breadcrumbs)->values()->map(fn ($item, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'],
        ])->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @if ($products->count() > 0)
    {{-- Liste des produits de la page courante --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => $selectedCategory->name,
        'numberOfItems' => $products->total(),
        'itemListElement' => $products->values()->map(fn ($p, $i) => [
            '@type' => 'ListItem',
            'position' => $products->firstItem() + $i,
            'url' => route('preview-article', $p->slug),
            'name' => $p->name,
        ])->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif
@endsection
@section('og_image', $ogImage)

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                {{ $selectedCategory->name ?? 'Notre Collection Exclusive' }}
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-6 sm:mb-8">
                {{ $selectedCategory->short_description ?: 'Découvrez des pièces uniques pour sublimer votre intérieur' }}
            </p>
            <div class="w-20 sm:w-24 h-1 bg-[#dfb54e] mx-auto rounded-full"></div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 sm:py-12 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Breadcrumb -->
            <div class="mb-6 sm:mb-8">
                <nav class="flex" aria-label="Fil d'Ariane">
                    <ol class="inline-flex flex-wrap items-center space-x-1 md:space-x-2">
                        @foreach ($breadcrumbs as $crumb)
                            @php $isLast = $loop->last; @endphp
                            <li class="inline-flex items-center">
                                @unless ($loop->first)
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endunless
                                @if ($isLast)
                                    <span class="ml-1 text-xs sm:text-sm font-medium text-gray-900 md:ml-2" aria-current="page">{{ $crumb['name'] }}</span>
                                @else
                                    <a href="{{ $crumb['url'] }}" class="ml-1 text-xs sm:text-sm font-medium text-gray-600 hover:text-[#dfb54e] transition-colors md:ml-2">{{ $crumb['name'] }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>

            {{-- Sous-catégories : maillage interne vers les landing pages enfants --}}
            @if ($subcategories->isNotEmpty())
                <section class="mb-8 sm:mb-10" aria-labelledby="sous-categories-titre">
                    <h2 id="sous-categories-titre" class="text-lg sm:text-xl font-bold text-gray-900 mb-4">
                        Explorer {{ $selectedCategory->name }}
                    </h2>
                    <ul class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach ($subcategories as $sub)
                            <li>
                                <a href="{{ $sub->url }}"
                                   class="group flex items-center justify-between gap-2 h-full px-4 py-3 bg-white border border-gray-100 rounded-xl shadow-sm hover:border-[#dfb54e] hover:shadow-md transition">
                                    <span class="text-sm font-medium text-gray-800 group-hover:text-[#dfb54e] transition-colors">{{ $sub->name }}</span>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#dfb54e] flex-shrink-0 transition-colors" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Sous-catégories sœurs : navigation latérale depuis une page enfant --}}
            @if ($siblings->isNotEmpty())
                <section class="mb-8 sm:mb-10" aria-labelledby="siblings-titre">
                    <h2 id="siblings-titre" class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        Aussi dans {{ $selectedCategory->parent->name }}
                    </h2>
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($siblings as $sibling)
                            <li>
                                <a href="{{ $sibling->url }}"
                                   class="inline-block px-3 py-1.5 text-sm text-gray-700 bg-gray-50 border border-gray-100 rounded-full hover:bg-white hover:border-[#dfb54e] hover:text-[#dfb54e] transition">
                                    {{ $sibling->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Mention affichée seulement si un seuil est réellement configuré :
                 aucun montant n'est inventé. --}}
            @if ((float) ($freeShippingLimit ?? 0) > 0)
                <p class="text-green-600 mb-6 text-center">
                    Livraison gratuite pour les commandes supérieures à {{ $freeShippingLimit }} DT
                </p>
            @endif

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
                                        <a href="{{ $category->url }}"
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
                                    <a href="{{ route('preview-article', $product->slug) }}" class="block h-full">
                                        <img src="{{ $product->cover_url }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                             loading="lazy" width="500" height="500" decoding="async"
                                             sizes="(min-width: 1280px) 25vw,
                                                    (min-width: 1024px) 33vw,
                                                    (min-width: 640px) 50vw,
                                                    100vw"/>
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
                                        {{ Str::limit($product->short_description, 80) }}
                                    </p>
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.59-.921 1.89 0l1.59 3.18a1 0 00.89.67h3.43c.98 0 1.39 1.25.6 1.82l-2.78 2.02a1 0 00-.34 1.13l1.06 3.19c.3.91-.76 1.67-1.54 1.1l-2.78-2.02a1 0 00-1.16 0l-2.78 2.02c-.78.57-1.84-.19-1.54-1.1l1.06-3.19a1 0 00-.34-1.13L2.49 8.79c-.79-.57-.38-1.82.6-1.82h3.43a1 0 00.89-.67l1.59-3.18z"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ ($product->approved_reviews_count ?? 0) > 0
                                                ? number_format($product->approved_reviews_avg, 1)
                                                : '5.0' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-black font-bold text-lg">
                                            @if ($product->has_variants)<span class="block text-xs font-normal text-gray-500">À partir de</span>@endif
                                            {{ number_format($product->price, 2) }} DT
                                        </p>
                                        @if ($product->has_variants)
                                            <a href="{{ route('preview-article', $product->slug) }}" aria-label="Choisir une option pour {{ $product->name }}"
                                               class="flex items-center justify-center bg-[#dfb54e] hover:bg-[#cba640] text-white p-2 rounded-lg transition-all duration-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @else
                                        <button aria-label="Ajouter {{ $product->name }} au panier"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-image="{{ $product->cover_url }}"
                                                data-stock="{{ $product->stock }}"
                                                class="flex items-center justify-center gap-1 bg-[#dfb54e] hover:bg-[#cba640] text-white p-2 rounded-lg transition-all duration-300 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $product->stock == 0 ? 'disabled' : '' }}
                                                onclick="addToCart(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                        @endif
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

        {{-- Contenu éditorial après les produits : l'utilisateur vient d'abord
             chercher des articles, le texte long sert la lecture approfondie. --}}
        @if ($selectedCategory->hasDistinctLongDescription())
            <div class="container mx-auto px-4 sm:px-6">
                <section class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden" aria-labelledby="description-categorie">
                    <div class="p-6">
                        <h2 id="description-categorie" class="text-xl md:text-2xl font-bold text-gray-900 mb-4">
                            {{ $selectedCategory->name }} : bien choisir
                        </h2>
                        {{-- HTML déjà nettoyé par App\Support\RichText. --}}
                        <div class="product-description max-w-none text-gray-700">
                            {!! $selectedCategory->safe_description !!}
                        </div>
                    </div>
                </section>
            </div>
        @endif

        <div class="container mx-auto px-4 sm:px-6">
            @include('front-office.partials.faq', [
                'faqs' => $categoryFaqs,
                'heading' => 'Questions fréquentes sur ' . $selectedCategory->name,
                'uid' => 'faq-categorie',
            ])
        </div>
    </section>
@endsection

@section('css')
<style>
    .product-description { line-height: 1.75; overflow-wrap: anywhere; }
    .product-description > *:first-child { margin-top: 0; }
    .product-description p { margin: 0 0 1rem; }
    .product-description h2 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 1.75rem 0 .75rem; }
    .product-description h3 { font-size: 1.05rem; font-weight: 600; color: #374151; margin: 1.5rem 0 .5rem; }
    .product-description ul, .product-description ol { margin: 0 0 1rem; padding-left: 1.5rem; }
    .product-description ul { list-style: disc; }
    .product-description ol { list-style: decimal; }
    .product-description li { margin-bottom: .35rem; }
    .product-description a { color: #b8860b; text-decoration: underline; }
    .product-description strong { font-weight: 600; color: #111827; }
</style>
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
@include('front-office.partials.faq-js')

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
        // addToCart est défini une seule fois dans front-office/layouts/app.blade.php
        // (notification, ouverture du panier, compteur, évènement de tracking).
</script>
@endsection
