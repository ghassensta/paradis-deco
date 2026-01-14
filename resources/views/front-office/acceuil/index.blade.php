@extends('front-office.layouts.app')
{{-- =========================
     MÉTADONNÉES SEO OPTIMISÉES
========================== --}}
@section('title', 'Paradis Déco – Boutique déco en ligne Tunisie | Sublimez votre intérieur')

@section('meta')
    @php
        // — Mots‑clés principaux
        $keywords = [
            'décoration intérieure Tunisie',
            'boutique déco en ligne',
            'vente décoration maison Tunisie',
            'meubles tunisiens', // etc. (10 maxi)
            'luminaires Tunisie',
            'tapis artisanaux Tunisie',
            'accessoires maison',
            'artisanat tunisien',
            'ameublement moderne',
            'cadeaux maison Tunisie',
        ];
        // — Image Open Graph
        $ogImage = asset('images/og/paradis-deco.jpg');
    @endphp

    <meta name="description"
        content="Paradis Déco, boutique déco n°1 en Tunisie : meubles, luminaires, tapis et accessoires artisanaux pour un intérieur moderne et chaleureux. Livraison rapide partout en Tunisie.">

    <meta name="keywords" content="{{ implode(', ', $keywords) }}">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

    <link rel="canonical" href="{{ config('app.url', 'https://example.com') }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    <!-- Robots -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:site_name" content="Paradis Déco">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Paradis Déco – Boutique déco en ligne Tunisie | Sublimez votre intérieur">
    <meta property="og:description"
        content="Meubles, luminaires & accessoires maison fabriqués ou sélectionnés en Tunisie.">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="Salon décoré avec meubles et luminaires de Paradis Déco">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Paradis Déco – Sublimez votre intérieur">
    <meta name="twitter:description" content="Boutique déco en ligne n°1 en Tunisie. Livraison rapide.">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endsection

@section('content')
    <!-- HERO avec une seule image -->
    <section class="relative overflow-hidden">
        <div id="carouselContainer" class="relative w-full">
            <div class="carousel-slide">
                <!-- Slide 1 -->
                <div class="relative w-full">
                    @php
                        // Si $config->homepage_banner existe → on génère l’URL publique « storage/... »
                        // Sinon on utilise l’image de secours Unsplash
                        $bannerUrl = $config->homepage_banner
                            ? asset('storage/' . $config->homepage_banner)
                            : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1740&q=80';
                    @endphp

                    <img src="{{ $bannerUrl }}" alt="Bannière {{ $config->site_name }}"
                        class="w-full h-full object-cover" loading="lazy" />

                    <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center px-6">
                        <h1 class="text-4xl md:text-6xl font-extrabold text-white drop-shadow-lg mb-4 animate-fade-in">
                            {{ $config->meta_title }}
                        </h1>

                        <p class="text-lg md:text-2xl text-gray-200 max-w-2xl mb-6 animate-fade-in-delay">
                            {{ $config->meta_description }}
                        </p>

                        <a href="{{ route('allproduits') }}"
                            class="inline-block px-8 py-3 bg-white text-primary rounded-full font-bold
                  hover:bg-primary hover:text-yellow transition animate-fade-in-delay
                  shadow-lg hover:shadow-xl">
                            Découvrir la collection
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Shape divider -->
        <div class="custom-shape-divider-bottom-1649125620">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                    class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- CATÉGORIES -->
    <section class="py-16 bg-white" id="categories">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Nos Catégories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @forelse ($latestCategories as $item)
                    <a href="{{ route('categorie.produits', $item->slug) }}"
                        class="group relative overflow-hidden rounded-xl shadow-md h-48">
                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/500x300?text=Category' }}"
                            alt="{{ $item->name }} | {{ $config->site_name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <h3 class="text-white text-xl font-bold group-hover:scale-110 transition">
                                {{ $item->name }}
                            </h3>
                        </div>
                    </a>
                @empty
                    <div class="col-span-4 text-center text-gray-500">
                        Aucune catégorie trouvée.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- NOUVEAUTÉS -->
    {{-- resources/views/components/section-nouveautes.blade.php --}}
    <section id="nouveautes" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <!-- Titre -->
            <div class="text-center mb-12">
                <span class="inline-block bg-primary-light px-4 py-2 rounded-full text-primary font-semibold mb-3">
                    Nouveautés
                </span>
                <h2 class="text-3xl font-bold">Découvrez Nos Dernières Créations</h2>
                <div class="w-24 h-1 bg-primary mx-auto mt-4"></div>
            </div>

            <!-- CAROUSEL -->
            <div class="relative">
                <!-- flèches -->
                <button class="swiper-button-prev !text-[#dfb54e] hover:scale-110 transition"
                    aria-label="Précédent"></button>
                <button class="swiper-button-next text !text-[#dfb54e] transition" aria-label="Suivant"></button>

                <!-- wrapper Swiper -->
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @forelse ($latestProducts as $item)
                            @if ($item->is_active)
                                <div class="swiper-slide">
                                    <!-- CARTE PRODUIT -->
                                 <!-- CARTE PRODUIT -->
<article
    class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 h-[460px] flex flex-col justify-between"
    itemscope itemtype="http://schema.org/Product">
    <!-- Image -->
    <div class="relative h-64 overflow-hidden flex items-center justify-center bg-gray-100">
        <a href="{{ route('preview-article', $item->slug) }}" title="{{ $item->meta_title ?? $item->name }}"
            class="block w-full h-full">
            <img src="{{ asset('storage/' . $item->image_avant) }}"
                alt="{{ $item->name }} | {{ $config->site_name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out"
                loading="lazy" itemprop="image">
        </a>

        <!-- Badge "Nouveau" -->
        <div class="absolute top-4 right-4">
            <span class="bg-[#228B22] text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">
                Nouveau
            </span>
        </div>

        <!-- Stock -->
        @if ($item->stock <= 5 && $item->stock > 0)
            <div class="absolute top-4 left-4">
                <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">
                    Stock faible
                </span>
            </div>
        @elseif ($item->stock == 0)
            <div class="absolute top-4 left-4">
                <span class="bg-gray-600 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">
                    Épuisé
                </span>
            </div>
        @endif
    </div>

    <!-- Contenu -->
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 hover:text-yellow-600 transition-colors line-clamp-1"
                itemprop="name">
                <a href="{{ route('preview-article', $item->slug) }}">
                    {{ Str::limit($item->name, 50) }}
                </a>
            </h3>
            <p class="text-gray-500 text-sm mt-1 line-clamp-2">
                {{ Str::limit(strip_tags($item->description), 80) }}
            </p>
        </div>

        <!-- Prix & action -->
        <div class="flex justify-between items-center mt-auto">
    <p class="text-black font-bold text-xl" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
        <span itemprop="price">{{ number_format($item->price, 2) }}</span> DT
        <meta itemprop="priceCurrency" content="TND">
        <meta itemprop="availability" content="{{ $item->stock > 0 ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock' }}">
        <link itemprop="url" href="{{ route('produits.show', $item->slug) }}">
        <!-- Correction pour Google : ajouter priceValidUntil -->
        <meta itemprop="priceValidUntil" content="{{ now()->addYear()->format('Y-m-d') }}">
    </p>

    <button aria-label="Ajouter {{ $item->name }} au panier"
        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
        data-price="{{ $item->price }}" data-image="{{ asset('storage/' . $item->images[0]) }}"
        data-stock="{{ $item->stock }}"
        class="flex items-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition transform hover:scale-105"
        @if ($item->stock == 0) disabled @endif
        onclick="addToCart(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Ajouter
    </button>
</div>


    </div>

    <!-- SEO -->
    <meta itemprop="description" content="{{ $item->meta_description ?? Str::limit($item->description, 80) }}">
    <meta itemprop="url" content="{{ route('preview-article', $item->slug) }}">
</article>

                                </div>
                            @endif
                        @empty
                            <div class="swiper-slide">
                                <div class="text-center py-8">
                                    <p class="text-gray-500 text-lg">Aucun produit disponible pour le moment.</p>
                                    <a href="{{ route('produits.index') }}"
                                        class="text-indigo-600 hover:underline font-semibold">
                                        Voir tous les produits
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- CTA voir tout -->
            <div class="text-center mt-12">
                <a href="{{ route('allproduits') }}"
                    class="inline-block px-8 py-3 border-2 border-primary text-primary rounded-full font-bold  transition">
                    Voir toutes les nouveautés
                </a>
            </div>
        </div>
    </section>


    <!-- SERVICES EXCLUSIFS -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Nos Services Exclusifs</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-xl text-center hover:shadow-lg transition">
                    <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-palette text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Conseil en Décoration</h3>
                    <p class="text-gray-600">Nos experts vous accompagnent pour créer un intérieur qui vous ressemble.
                    </p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl text-center hover:shadow-lg transition">
                    <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-truck text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Livraison & Installation</h3>
                    <p class="text-gray-600">Nous livrons et installons vos meubles pour un service clé en main.</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl text-center hover:shadow-lg transition">
                    <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-undo-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Retour Facile</h3>
                    <p class="text-gray-600">30 jours pour changer d'avis, retour gratuit en magasin.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- resources/views/front-office/partials/inspirations.blade.php --}}
    @if ($inspirations->isNotEmpty())
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                {{-- Titre + intro --}}
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold">Inspirations Déco</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Découvrez nos ambiances tendances et trouvez l'inspiration pour votre intérieur.
                    </p>
                </div>

                {{-- Grille --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($inspirations as $inspiration)
                        <a href="{{ route('preview-inspiration', $inspiration->slug) }}"
                            class="relative group overflow-hidden rounded-xl h-64">
                            {{-- Image --}}
                            <img src="{{ asset('storage/' . $inspiration->image) }}" alt="{{ $inspiration->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                loading="lazy">
                            {{-- Overlay --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6
                               opacity-0 group-hover:opacity-100 transition duration-300">
                                <div>
                                    <h3 class="text-white text-xl font-bold">{{ $inspiration->title }}</h3>
                                    @if (!empty($inspiration->resume))
                                        <p class="text-gray-200 text-sm">{{ $inspiration->resume }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        {{-- Fallback si la requête renvoie quand même vide --}}
                        <p class="col-span-full text-center text-gray-500">
                            Aucune inspiration disponible pour le moment.
                        </p>
                    @endforelse
                </div>

                {{-- Bouton « Voir plus » uniquement si on a ≥4 éléments (ou selon ton critère) --}}
                @if ($inspirations->count() >= 4)
                    <div class="text-center mt-12">
                        <a href="{{ route('allinspirations') }}"
                            class="inline-block px-8 py-3 bg-black text-white rounded-full font-bold
                          hover:bg-primary-dark transition">
                            Voir plus d'inspirations
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- TÉMOIGNAGES -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Ils Nous Ont Fait Confiance</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Découvrez ce que nos clients disent de notre sélection et de
                    notre service.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($testimonials as $testimonial)
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star{{ $i <= $testimonial->rating ? ' text-yellow-400' : ' text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">{{ Str::limit($testimonial->comment, 150, '...') }}</p>
                        <div class="flex items-center">
                            <img src="https://i.pravatar.cc/128?img={{ rand(1, 70) }}" loading="lazy"  alt="{{ $testimonial->name }} | {{ $config->site_name }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <div>
                                <h3 class="font-bold">{{ $testimonial->name }}</h3>
                                <p class="text-gray-500 text-sm">{{ $testimonial->location ?? 'Non spécifié' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center">
                        <p class="text-gray-600">Aucun témoignage disponible pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <style>
        /* Carousel styles */
        .carousel-slide {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-slide img {
            object-fit: cover;
            width: 100%;
            height: 80vh;
        }

        /* Hide scrollbar for products carousel */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Fade-in animations */
        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.8s ease-in-out forwards;
        }

        .animate-fade-in-delay {
            animation: fade-in 1.2s ease-in-out forwards;
        }

        /* Custom animations */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }


        /* Custom shapes */
        .custom-shape-divider-bottom-1649125620 {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }

        .custom-shape-divider-bottom-1649125620 svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 150px;
        }

        .custom-shape-divider-bottom-1649125620 .shape-fill {
            fill: #FFFFFF;
        }

        /* Lazy loading placeholder */
        img.lazy {
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }

        img.lazy::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Loading bar */
        #loadingBar {
            transition: transform 0.5s ease-in-out;
        }

        /* Loading button state */
        button.loading {
            background-color: #a5b4fc;
            cursor: wait;
        }

        /* Enhanced cart styles */
        #cartOffcanvas {
            font-family: 'Inter', sans-serif;
        }

        #cartItems .flex {
            background-color: #fff;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        #cartItems .flex:hover {
            background-color: #f9fafb;
            transform: translateY(-2px);
        }

        #cartItems img {
            border: 1px solid #e5e7eb;
        }

        #cartItems button {
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        #cartItems .border {
            border-color: #e5e7eb;
        }

        #cartItems .font-medium {
            color: #1f2937;
        }



        a[href="/checkout"]:hover {
            transform: scale(1.02);
        }

        #continueShoppingBtn {
            transition: color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
        }

        #continueShoppingBtn:hover {
            transform: scale(1.02);
        }

        /* Product Card Enhancements */
        article {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        article:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        article img {
            transition: transform 0.5s ease-in-out;
        }

        article button:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
            opacity: 0.7;
        }

        article button:not(:disabled):hover {
            transform: scale(1.05);
        }



        /* Mini Cart */
        #miniCart {
            transition: opacity 0.2s ease-in-out;
        }

        #miniCart.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #miniCart:not(.hidden) {
            opacity: 1;
        }

        @media (max-width: 640px) {
            article {
                max-width: 100%;
            }

            article .h-64 {
                height: 200px;
            }

            article .text-lg {
                font-size: 1rem;
            }

            article .text-xl {
                font-size: 1.125rem;
            }
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Swiper('.mySwiper', {
                slidesPerView: 1.2, // 1 slide + aperçu
                spaceBetween: 20,
                loop: true,
                grabCursor: true,
                autoplay: {
                    delay: 6500
                },
                breakpoints: { // responsive
                    640: {
                        slidesPerView: 2
                    },
                    768: {
                        slidesPerView: 3
                    },
                    1024: {
                        slidesPerView: 4
                    }
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                }
            });
        });
    </script>
@endsection
