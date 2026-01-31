@extends('front-office.layouts.app')

{{-- =========================
     MÉTADONNÉES SEO OPTIMISÉES
========================== --}}
@section('title', 'Paradis Déco – Boutique déco en ligne Tunisie | Sublimez votre intérieur')

@section('meta')
    @php
        $keywords = [
            'décoration intérieure Tunisie',
            'boutique déco en ligne',
            'vente décoration maison Tunisie',
            'meubles tunisiens',
            'luminaires Tunisie',
            'tapis artisanaux Tunisie',
            'accessoires maison',
            'artisanat tunisien',
            'ameublement moderne',
            'cadeaux maison Tunisie',
        ];

        $ogImage = asset('images/og/paradis-deco.jpg');
    @endphp

    <meta name="description" content="Paradis Déco, boutique déco n°1 en Tunisie : meubles, luminaires, tapis et accessoires artisanaux pour un intérieur moderne et chaleureux. Livraison rapide partout en Tunisie.">
    <meta name="keywords" content="{{ implode(', ', $keywords) }}">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:site_name" content="Paradis Déco">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Paradis Déco – Boutique déco en ligne Tunisie | Sublimez votre intérieur">
    <meta property="og:description" content="Meubles, luminaires & accessoires maison fabriqués ou sélectionnés en Tunisie.">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="Salon décoré avec meubles et luminaires de Paradis Déco">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Paradis Déco – Sublimez votre intérieur">
    <meta name="twitter:description" content="Boutique déco en ligne n°1 en Tunisie. Livraison rapide.">
    <meta name="twitter:image" content="{{ $ogImage }}">

@endsection

@section('content')
    <!-- Le reste de ton contenu HTML reste IDENTIQUE, on a juste retiré les microdata -->

    <!-- HERO -->
    <section class="relative overflow-hidden">
        <div id="carouselContainer" class="relative w-full">
            <div class="carousel-slide">
                <div class="relative w-full">
                    @php
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
                           class="inline-block px-8 py-3 bg-white text-primary rounded-full font-bold hover:bg-primary hover:text-yellow transition animate-fade-in-delay shadow-lg hover:shadow-xl">
                            Découvrir la collection
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="custom-shape-divider-bottom-1649125620">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
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

    <!-- NOUVEAUTÉS (cartes produits sans microdata) -->
    <section id="nouveautes" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <span class="inline-block bg-primary-light px-4 py-2 rounded-full text-primary font-semibold mb-3">Nouveautés</span>
                <h2 class="text-3xl font-bold">Découvrez Nos Dernières Créations</h2>
                <div class="w-24 h-1 bg-primary mx-auto mt-4"></div>
            </div>

            <div class="relative">
                <button class="swiper-button-prev !text-[#dfb54e] hover:scale-110 transition" aria-label="Précédent"></button>
                <button class="swiper-button-next !text-[#dfb54e] hover:scale-110 transition" aria-label="Suivant"></button>

                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @forelse ($latestProducts as $item)
                            @if ($item->is_active)
                                <div class="swiper-slide">
                                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 h-[460px] flex flex-col justify-between">
                                        <div class="relative h-64 overflow-hidden flex items-center justify-center bg-gray-100">
                                            <a href="{{ route('preview-article', $item->slug) }}" title="{{ $item->meta_title ?? $item->name }}" class="block w-full h-full">
                                                <img src="{{ asset('storage/' . $item->image_avant) }}"
                                                     alt="{{ $item->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                     loading="lazy">
                                            </a>

                                            <div class="absolute top-4 right-4">
                                                <span class="bg-[#228B22] text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Nouveau</span>
                                            </div>

                                            @if ($item->stock <= 5 && $item->stock > 0)
                                                <div class="absolute top-4 left-4">
                                                    <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Stock faible</span>
                                                </div>
                                            @elseif ($item->stock == 0)
                                                <div class="absolute top-4 left-4">
                                                    <span class="bg-gray-600 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Épuisé</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-5 flex-1 flex flex-col justify-between">
                                            <div class="mb-4">
                                                <h3 class="text-lg font-semibold text-gray-800 hover:text-yellow-600 transition-colors line-clamp-1">
                                                    <a href="{{ route('preview-article', $item->slug) }}">
                                                        {{ Str::limit($item->name, 50) }}
                                                    </a>
                                                </h3>
                                                <p class="text-gray-500 text-sm mt-1 line-clamp-2">
                                                    {{ Str::limit(strip_tags($item->description), 80) }}
                                                </p>
                                            </div>

                                            <div class="flex justify-between items-center mt-auto">
                                                <p class="text-black font-bold text-xl">
                                                    {{ number_format($item->price, 2) }} DT
                                                </p>

                                                <button aria-label="Ajouter {{ $item->name }} au panier"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price ?? 0 }}"
                                                        data-image="{{ asset('storage/' . $item->image_avant) }}"
                                                        data-stock="{{ $item->stock }}"
                                                        class="flex items-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition transform hover:scale-105"
                                                        @if ($item->stock == 0) disabled @endif
                                                        onclick="addToCart(this)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Ajouter
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endif
                        @empty
                            <div class="swiper-slide">
                                <div class="text-center py-8">
                                    <p class="text-gray-500 text-lg">Aucun produit disponible pour le moment.</p>
                                    <a href="{{ route('produits.index') }}" class="text-indigo-600 hover:underline font-semibold">
                                        Voir tous les produits
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('allproduits') }}"
                   class="inline-block px-8 py-3 border-2 border-primary text-primary rounded-full font-bold transition hover:bg-primary hover:text-white">
                    Voir toutes les nouveautés
                </a>
            </div>
        </div>
    </section>

    <!-- Le reste de tes sections (Services, Inspirations, Témoignages) reste inchangé -->

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
