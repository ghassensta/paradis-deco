@extends('front-office.layouts.app')

@section('title', '{{ $product->name }} | Paradis Déco')

@section('meta')
    <meta name="description" content="{{ $product->meta_description ?? Str::limit($product->description, 160) }}">
    <meta name="keywords" content="{{ $product->meta_keywords ?? 'bougie parfumée, décoration maison, eucalyptus, menthe, artisanale, tunisie' }}">
    <meta property="og:title" content="{{ $product->name }} | Paradis Déco">
    <meta property="og:description" content="{{ $product->meta_description ?? Str::limit($product->description, 160) }}">
    <meta property="og:image" content="{{ asset('storage/' . $product->images[0]) }}">
    <meta property="og:url" content="{{ route('product.show', $product->slug) }}">
    <meta property="og:type" content="product">
    <link rel="canonical" href="{{ route('product.show', $product->slug) }}" />
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{{ $product->name }}",
        "image": "{{ asset('storage/' . $product->images[0]) }}",
        "description": "{{ $product->description }}",
        "brand": {
            "@type": "Brand",
            "name": "Paradis Déco"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ route('product.show', $product->slug) }}",
            "priceCurrency": "TND",
            "price": "{{ number_format($product->price, 2) }}",
            "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "itemCondition": "https://schema.org/NewCondition"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $product->reviews->avg('rating') ?? 4.8 }}",
            "reviewCount": "{{ $product->reviews->count() ?? 24 }}"
        }
    }
    </script>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-600">Accueil</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-500 hover:text-indigo-600">Produits</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('category.show', $product->category->slug) }}" class="text-gray-500 hover:text-indigo-600">{{ $product->category->name }}</a></li>
                    <li class="text-gray-400">/</li>
                    <li><span class="text-indigo-600">{{ $product->name }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="lg:sticky lg:top-20">
                <div class="relative mb-4 bg-white rounded-xl shadow-sm overflow-hidden group">
                    <img id="mainImage" data-src="{{ asset('storage/' . $product->images[0]) }}"
                         alt="{{ $product->name }}" class="w-full h-96 object-contain main-image transform group-hover:scale-105 transition-transform duration-300 lazy" />
                    <button id="prevImage"
                            class="gallery-nav absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md opacity-0 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Image précédente">
                        <i class="fas fa-chevron-left text-gray-700"></i>
                    </button>
                    <button id="nextImage"
                            class="gallery-nav absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md opacity-0 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Image suivante">
                        <i class="fas fa-chevron-right text-gray-700"></i>
                    </button>
                </div>
                <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
                    @foreach ($product->images as $index => $image)
                        <img data-src="{{ asset('storage/' . $image) }}"
                             alt="{{ $product->name }} - vue {{ $index + 1 }}"
                             class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 {{ $index == 0 ? 'border-indigo-600 active' : 'border-transparent' }} transform transition-all duration-300 hover:scale-105 hover:border-indigo-300 lazy"
                             data-index="{{ $index }}" />
                    @endforeach
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <!-- Product Header -->
                    <div class="mb-4">
                        @if ($product->is_new)
                            <span class="inline-block bg-indigo-100 text-indigo-600 text-xs font-semibold px-2 py-1 rounded mb-2">Nouveauté</span>
                        @endif
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas {{ $i <= floor($product->reviews->avg('rating') ?? 4.8) ? 'fa-star' : ($i - 0.5 <= ($product->reviews->avg('rating') ?? 4.8) ? 'fa-star-half-alt' : 'fa-star') }}"></i>
                                @endfor
                            </div>
                            <a href="#reviews" class="text-sm text-gray-500 hover:text-indigo-600">{{ $product->reviews->count() ?? 24 }} avis</a>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }} flex items-center">
                                <i class="fas {{ $product->stock > 0 ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $product->stock > 0 ? 'En stock' : 'Épuisé' }}
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DT</p>
                        <p class="text-sm text-gray-500">TVA incluse</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Description</h2>
                        <p class="text-gray-600 mb-4">{!! $product->description !!}</p>
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Durée de combustion : environ {{ $product->burn_time ?? 50 }} heures</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Contenance : {{ $product->weight ?? 300 }}g</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Dimensions : {{ $product->dimensions ?? '8cm x 10cm' }}</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Fabriquée en Tunisie</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Color Variant -->
                    @if ($product->variants->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Couleur</h3>
                            <div class="flex space-x-3">
                                @foreach ($product->variants as $variant)
                                    <button
                                        class="w-10 h-10 rounded-full bg-{{ $variant->color }}-100 border-2 {{ $loop->first ? 'border-{{ $variant->color }}-300' : 'border-transparent' }} hover:border-{{ $variant->color }}-300 focus:ring-2 focus:ring-{{ $variant->color }}-500"
                                        data-variant-id="{{ $variant->id }}"
                                        aria-label="Sélectionner la couleur {{ $variant->color }}"
                                        onclick="selectVariant(this, {{ $variant->id }})"></button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity & Add to Cart -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button class="px-3 py-2 text-gray-600 hover:text-indigo-600" onclick="updateQuantity(-1)"
                                        aria-label="Diminuer la quantité">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantity" class="px-3 py-1">1</span>
                                <button class="px-3 py-2 text-gray-600 hover:text-indigo-600" onclick="updateQuantity(1)"
                                        aria-label="Augmenter la quantité">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-lg font-medium transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ asset('storage/' . $product->images[0]) }}"
                                data-stock="{{ $product->stock }}"
                                onclick="addToCart(this)"
                                aria-label="Ajouter {{ $product->name }} au panier"
                                {{ $product->stock == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart mr-2"></i>
                                <span>Ajouter au panier</span>
                                <svg class="animate-spin h-5 w-5 text-white hidden ml-2" id="addToCartSpinner"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-indigo-600 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-truck text-indigo-600 mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold mb-1">Livraison rapide</h3>
                                <p class="text-sm text-gray-600">
                                    Livraison en 24-48h à Tunis et 2-3 jours pour le reste du pays.
                                    <a href="/livraison" class="text-indigo-600 hover:underline">En savoir plus</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Share -->
                    <div class="flex items-center pt-4 border-t border-gray-200">
                        <span class="text-sm text-gray-600 mr-3">Partager :</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('product.show', $product->slug)) }}"
                           class="text-gray-500 hover:text-blue-500 mx-1" target="_blank" aria-label="Partager sur Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/?url={{ urlencode(route('product.show', $product->slug)) }}"
                           class="text-gray-500 hover:text-pink-500 mx-1" target="_blank" aria-label="Partager sur Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('product.show', $product->slug)) }}&text={{ urlencode($product->name) }}"
                           class="text-gray-500 hover:text-blue-400 mx-1" target="_blank" aria-label="Partager sur Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(route('product.show', $product->slug)) }}&media={{ urlencode(asset('storage/' . $product->images[0])) }}"
                           class="text-gray-500 hover:text-red-500 mx-1" target="_blank" aria-label="Partager sur Pinterest">
                            <i class="fab fa-pinterest-p"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div id="reviews" class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Avis clients</h3>
                    <div class="flex items-center mb-6">
                        <div class="mr-4">
                            <span class="text-4xl font-bold">{{ number_format($product->reviews->avg('rating') ?? 4.8, 1) }}</span>
                            <span class="text-gray-500">/5</span>
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                <div class="flex text-yellow-400 mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas {{ $i <= floor($product->reviews->avg('rating') ?? 4.8) ? 'fa-star' : ($i - 0.5 <= ($product->reviews->avg('rating') ?? 4.8) ? 'fa-star-half-alt' : 'fa-star') }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">{{ $product->reviews->count() ?? 24 }} avis</span>
                            </div>
                            @for ($i = 5; $i >= 3; $i--)
                                @php
                                    $percentage = $product->reviews->where('rating', $i)->count() / ($product->reviews->count() ?: 1) * 100;
                                @endphp
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="mr-2">{{ $i }} étoiles</span>
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span>{{ round($percentage) }}%</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <a href="{{ route('product.review.create', $product->slug) }}"
                       class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-pen mr-2"></i> Écrire un avis
                    </a>
                </div>
                <div class="space-y-6">
                    @forelse ($product->reviews->take(3) as $review)
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center mb-3">
                                <div class="flex text-yellow-400 mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas {{ $i <= $review->rating ? 'fa-star' : 'fa-star' }} {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-semibold mb-1">{{ $review->title }}</h4>
                            <p class="text-gray-600 mb-2">{{ $review->comment }}</p>
                            <div class="flex items-center">
                                <img data-src="{{ $review->user->avatar ?? 'https://randomuser.me/api/portraits/women/68.jpg' }}"
                                     alt="{{ $review->user->name }}" class="w-8 h-8 rounded-full mr-2 lazy">
                                <span class="text-sm font-medium">{{ $review->user->name }}, {{ $review->user->city ?? 'Tunis' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600">Aucun avis pour ce produit.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-6">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($similarProducts as $item)
                    @if ($item->is_active)
                        <article
                            class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                            itemscope itemtype="http://schema.org/Product">
                            <!-- Image Section -->
                            <div class="relative overflow-hidden">
                                <a href="{{ route('preview-article', $item->slug) }}" target="_blank"
                                   title="{{ $item->meta_title ?? $item->name }}" class="block">
                                    <img src="{{ asset('storage/' . $item->images[0]) }}" alt="{{ $item->name }}"
                                         class="h-64 w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out"
                                         loading="lazy" itemprop="image" />
                                </a>
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="bg-[#228B22] text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Nouveau</span>
                                </div>
                                @if ($item->stock <= 5 && $item->stock > 0)
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Stock
                                            faible</span>
                                    </div>
                                @elseif ($item->stock == 0)
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-gray-600 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Épuisé</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Section -->
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors"
                                            itemprop="name">
                                            <a href="">{{ $item->name }}</a>
                                        </h3>
                                        <p class="text-gray-500 text-sm mt-1">{{ Str::limit($item->description, 50) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center" itemprop="aggregateRating" itemscope
                                         itemtype="http://schema.org/AggregateRating">
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        <span class="text-gray-600 text-sm ml-1" itemprop="ratingValue">4.8</span>
                                        <meta itemprop="reviewCount" content="120">
                                    </div>
                                </div>

                                <!-- Price and Action -->
                                <div class="flex justify-between items-center">
                                    <p class="text-black font-bold text-xl" itemprop="offers" itemscope
                                       itemtype="http://schema.org/Offer">
                                        <span itemprop="price">{{ number_format($item->price, 2) }}</span> DT
                                        <meta itemprop="priceCurrency" content="TND">
                                    </p>
                                    <button aria-label="Ajouter {{ $item->name }} au panier"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-price="{{ $item->price }}"
                                            data-image="{{ asset('storage/' . $item->images[0]) }}"
                                            data-stock="{{ $item->stock }}"
                                            class="cursor-pointer flex items-center justify-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 transform hover:scale-105"
                                            @if ($item->stock == 0) disabled @endif onclick="addToCart(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Ajouter
                                    </button>
                                </div>
                            </div>

                            <!-- SEO Meta -->
                            <meta itemprop="description"
                                  content="{{ $item->meta_description ?? Str::limit($item->description, 80) }}">
                            <meta itemprop="url" content="">
                        </article>
                    @endif
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500 text-lg">Aucun produit disponible pour le moment.</p>
                        <a href="{{ route('products.index') }}"
                           class="text-indigo-600 hover:underline font-semibold">Voir tous les produits</a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Values / Advantages -->
    <section class="container mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-12 justify-center text-center">
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                        <path d="M16 8h2l3 3v5a2 2 0 01-2 2h-1"></path>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-indigo-600 mb-2">Livraison Express</h3>
                <p class="text-gray-600">Partout en Tunisie sous 48h</p>
            </div>
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-indigo-600 mb-2">Satisfait ou Remboursé</h3>
                <p class="text-gray-600">Achetez sereinement</p>
            </div>
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 20c4 0 7-3 7-7V6"></path>
                        <path d="M5 9V6a7 7 0 0 1 14 0v3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-indigo-600 mb-2">Décorations Artisanales</h3>
                <p class="text-gray-600">Faites à la main, sélection locale</p>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        /* Gallery Animations */
        .main-image {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .main-image.fade-out {
            opacity: 0;
            transform: scale(0.95);
        }
        .main-image.fade-in {
            opacity: 1;
            transform: scale(1);
        }
        /* Navigation Arrows */
        .gallery-nav {
            transition: opacity 0.3s ease;
        }
        .gallery-nav:hover {
            opacity: 1;
        }
        /* Scrollbar Hide */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Add to Cart Button */
        button.loading #addToCartSpinner {
            display: inline-block;
        }
        button.loading {
            background-color: #a5b4fc;
            cursor: wait;
        }
        /* Variant Selection */
        button.border-transparent:hover {
            transform: scale(1.1);
        }
        button.border-green-300,
        button.border-blue-300,
        button.border-gray-300 {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Gallery Management
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('.flex.space-x-3 img');
            const prevButton = document.getElementById('prevImage');
            const nextButton = document.getElementById('nextImage');
            let currentIndex = 0;
            const images = Array.from(thumbnails).map(img => img.dataset.src);

            // Preload images
            images.forEach(src => {
                const img = new Image();
                img.src = src;
            });

            // Change main image
            const changeImage = (index) => {
                if (index < 0) index = images.length - 1;
                if (index >= images.length) index = 0;
                mainImage.classList.add('fade-out');
                setTimeout(() => {
                    mainImage.src = images[index];
                    mainImage.classList.remove('fade-out');
                    mainImage.classList.add('fade-in');
                    thumbnails.forEach(thumb => thumb.classList.remove('border-indigo-600', 'active'));
                    thumbnails[index].classList.add('border-indigo-600', 'active');
                    currentIndex = index;
                }, 300);
            };

            // Thumbnail clicks
            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => changeImage(index));
            });

            // Navigation arrows
            prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
            nextButton.addEventListener('click', () => changeImage(currentIndex + 1));

            // Swipe gestures for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            mainImage.parentElement.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            mainImage.parentElement.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) changeImage(currentIndex + 1); // Swipe left
                if (touchEndX - touchStartX > 50) changeImage(currentIndex - 1); // Swipe right
            });

            // Quantity Management
            let quantity = 1;
            const quantityDisplay = document.getElementById('quantity');
            window.updateQuantity = (change) => {
                quantity += change;
                if (quantity < 1) quantity = 1;
                if (quantity > {{ $product->stock }}) {
                    quantity = {{ $product->stock }};
                    showNotification('Stock maximum atteint.', 'error');
                }
                quantityDisplay.textContent = quantity;
            };

            // Variant Selection
            let selectedVariantId = {{ $product->variants->first()->id ?? null }};
            window.selectVariant = (button, variantId) => {
                selectedVariantId = variantId;
                document.querySelectorAll('.flex.space-x-3 button').forEach(btn => {
                    btn.classList.remove('border-green-300', 'border-blue-300', 'border-gray-300');
                    btn.classList.add('border-transparent');
                });
                button.classList.remove('border-transparent');
                button.classList.add(`border-${button.className.match(/bg-(\w+)-100/)[1]}-300`);
            };

            // Override addToCart to include quantity and variant
            window.addToCart = function(button) {
                const loadingBar = document.getElementById('loadingBar');
                loadingBar.classList.remove('hidden');
                loadingBar.classList.add('scale-x-100');
                button.classList.add('loading');
                button.disabled = true;

                const product = {
                    id: parseInt(button.dataset.id),
                    name: button.dataset.name,
                    price: parseFloat(button.dataset.price),
                    image: button.dataset.image,
                    stock: parseInt(button.dataset.stock),
                    variant_id: selectedVariantId,
                    quantity: quantity
                };

                if (!product.id || !product.name || isNaN(product.price) || !product.image || isNaN(product.stock)) {
                    showNotification('Données du produit invalides.', 'error');
                    resetButtonAndLoadingBar(button, loadingBar);
                    return;
                }

                if (product.stock === 0) {
                    showNotification('Produit épuisé.', 'error');
                    resetButtonAndLoadingBar(button, loadingBar);
                    return;
                }

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const existingItem = cart.find(item => item.id === product.id && item.variant_id === product.variant_id);

                if (existingItem) {
                    existingItem.quantity += product.quantity;
                    if (existingItem.quantity > product.stock) {
                        existingItem.quantity = product.stock;
                        showNotification('Stock maximum atteint pour ce produit.', 'error');
                    }
                } else {
                    cart.push(product);
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartUI();
                updateCartCount();
                updateMiniCart();
                showNotification('Produit ajouté au panier.');
                resetButtonAndLoadingBar(button, loadingBar);

                // Open cart offcanvas
                document.getElementById('cartOffcanvas').classList.remove('translate-x-full');
                document.getElementById('cartOverlay').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            // Initialize lazy loading
            lazyLoadImages();
        });
    </script>
@endsection
