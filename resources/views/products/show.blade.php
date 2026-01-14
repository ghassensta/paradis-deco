<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bougie Parfumée Eucalyptus | Paradis Déco</title>
    <meta name="description"
        content="Découvrez notre bougie parfumée aux notes d'eucalyptus et menthe - artisanale, longue durée (50h) - parfaite pour créer une ambiance relaxante">
    <meta name="keywords" content="bougie parfumée, décoration maison, eucalyptus, menthe, bougie artisanale, tunisie">
    <meta property="og:title" content="Bougie Parfumée Eucalyptus | Paradis Déco">
    <meta property="og:description"
        content="Bougie artisanale aux notes fraîches d'eucalyptus et menthe - 50h de combustion">
    <meta property="og:image" content="https://example.com/assets/img/bougie-eucalyptus.jpg">
    <meta property="og:url" content="https://example.com/produits/bougie-eucalyptus">
    <meta property="og:type" content="product">
    <link rel="canonical" href="https://example.com/produits/bougie-eucalyptus" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "Bougie Parfumée Eucalyptus & Menthe",
      "image": "https://example.com/assets/img/bougie-eucalyptus.jpg",
      "description": "Bougie artisanale parfumée aux notes fraîches d'eucalyptus et menthe avec 50 heures de combustion",
      "brand": {
        "@type": "Brand",
        "name": "Paradis Déco"
      },
      "offers": {
        "@type": "Offer",
        "url": "https://example.com/produits/bougie-eucalyptus",
        "priceCurrency": "TND",
        "price": "25.00",
        "availability": "https://schema.org/InStock",
        "itemCondition": "https://schema.org/NewCondition"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "24"
      }
    }
    </script>
    <style>
        /* Animations pour la galerie */
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

        /* Style pour les flèches de navigation */
        .gallery-nav {
            transition: opacity 0.3s ease;
        }

        .gallery-nav:hover {
            opacity: 1;
        }

        /* Désactiver le scrollbar par défaut */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <!-- Modal Panier (inchangé) -->
    <div id="cartModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div id="cartModalBackdrop" class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Votre Panier (3)</h3>
                        <button id="closeCartModal" type="button"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6">
                        <div class="flow-root">
                            <ul class="-my-6 divide-y divide-gray-200">
                                <li class="py-6 flex">
                                    <div
                                        class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                        <img src="https://imgs.search.brave.com/JJ72iD38GV95e3eg-Yz6howVFSBX5oYiKbOHtFRSSgc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/ZHVyYW5jZS5mci83/NzE4LWhvbWVfZGVm/YXVsdC9ib3VnaWUt/cGFyZnVtZWUtZXVj/YWx5cHR1cy1tZW50/aGUuanBn"
                                            alt="Bougie Eucalyptus" loading="lazy" class="w-full h-full object-center object-cover">
                                    </div>
                                    <div class="ml-4 flex-1 flex flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>Bougie Eucalyptus</h3>
                                                <p class="ml-4">25 DT</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Parfum menthe & eucalyptus</p>
                                        </div>
                                        <div class="flex-1 flex items-end justify-between text-sm">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button class="px-3 py-1 text-gray-600 hover:text-indigo-600">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <span class="px-2">1</span>
                                                <button class="px-3 py-1 text-gray-600 hover:text-indigo-600">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                            <div class="flex">
                                                <button type="button"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="py-6 flex">
                                    <div
                                        class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                        <img src="https://imgs.search.brave.com/yrXGHOIGNLQ2DV6krRHOvc8kFRVy4OkGqM6q8L6UoGs/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL0kv/ODFIZlV2d1hWbkwu/anBn"
                                            alt="Coussin Brodé" class="w-full h-full object-center object-cover">
                                    </div>
                                    <div class="ml-4 flex-1 flex flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>Coussin Brodé</h3>
                                                <p class="ml-4">55 DT</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Motif géométrique</p>
                                        </div>
                                        <div class="flex-1 flex items-end justify-between text-sm">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button class="px-3 py-1 text-gray-600 hover:text-indigo-600">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <span class="px-2">1</span>
                                                <button class="px-3 py-1 text-gray-600 hover:text-indigo-600">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                            <div class="flex">
                                                <button type="button"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Passer la commande
                    </button>
                    <button type="button" id="continueShopping"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Continuer mes achats
                    </button>
                </div>
                <div class="border-t border-gray-200 px-4 py-3">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Sous-total</p>
                        <p>80 DT</p>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500 mt-1">
                        <p>Livraison</p>
                        <p>10 DT</p>
                    </div>
                    <div
                        class="flex justify-between text-lg font-bold text-gray-900 mt-3 pt-3 border-t border-gray-200">
                        <p>Total</p>
                        <p>90 DT</p>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Taxes incluses. Frais de livraison calculés à la validation.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center">
                <img src="{{ asset('assets/img/logo-image.jpg') }}" alt="Paradis Déco" class="h-12" />
            </a>
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a href="/produits" class="text-gray-700 hover:text-indigo-600">Nos produits</a>
                <a href="/inspirations" class="text-gray-700 hover:text-indigo-600">Inspirations</a>
                <a href="/services" class="text-gray-700 hover:text-indigo-600">Services</a>
                <a href="/blog" class="text-gray-700 hover:text-indigo-600">Blog déco</a>
            </nav>
            <div class="flex items-center space-x-4">
                <button aria-label="Recherche" class="text-gray-600 hover:text-indigo-600">
                    <i class="fas fa-search"></i>
                </button>
                <a href="/panier" class="text-gray-600 hover:text-indigo-600 relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span
                        class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-indigo-600">Accueil</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="/produits" class="text-gray-500 hover:text-indigo-600">Produits</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="/produits/decoration" class="text-gray-500 hover:text-indigo-600">Décoration</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <span class="text-indigo-600">Bougie Eucalyptus</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- MAIN PRODUCT SECTION -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="lg:sticky lg:top-4">
                <div class="relative mb-4 bg-white rounded-xl shadow-sm overflow-hidden group">
                    <img id="mainImage"
                        src="https://imgs.search.brave.com/JJ72iD38GV95e3eg-Yz6howVFSBX5oYiKbOHtFRSSgc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/ZHVyYW5jZS5mci83/NzE4LWhvbWVfZGVm/YXVsdC9ib3VnaWUt/cGFyZnVtZWUtZXVj/YWx5cHR1cy1tZW50/aGUuanBn"
                        alt="Bougie parfumée eucalyptus et menthe"
                        class="w-full h-96 object-contain main-image transform group-hover:scale-105 transition-transform duration-300" />
                    <!-- Navigation Arrows -->
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
                    <img src="https://imgs.search.brave.com/JJ72iD38GV95e3eg-Yz6howVFSBX5oYiKbOHtFRSSgc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/ZHVyYW5jZS5mci83/NzE4LWhvbWVfZGVm/YXVsdC9ib3VnaWUt/cGFyZnVtZWUtZXVj/YWx5cHR1cy1tZW50/aGUuanBn"
                        alt="Bougie parfumée - vue 1"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-indigo-600 transform transition-all duration-300 hover:scale-105 active"
                        data-index="0" />
                    <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=500&q=80"
                        alt="Bougie parfumée - vue 2"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent transform transition-all duration-300 hover:scale-105 hover:border-indigo-300"
                        data-index="1" />
                    <img src="https://images.unsplash.com/photo-1601001815894-4bb6c81416f9?auto=format&fit=crop&w=500&q=80"
                        alt="Bougie parfumée - vue 3"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent transform transition-all duration-300 hover:scale-105 hover:border-indigo-300"
                        data-index="2" />
                    <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=500&q=80"
                        alt="Bougie parfumée - vue 4"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent transform transition-all duration-300 hover:scale-105 hover:border-indigo-300"
                        data-index="3" />
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <!-- Product Header -->
                    <div class="mb-4">
                        <span
                            class="inline-block bg-indigo-100 text-indigo-600 text-xs font-semibold px-2 py-1 rounded mb-2">Nouveauté</span>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Bougie Parfumée Eucalyptus &
                            Menthe</h1>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <a href="#reviews" class="text-sm text-gray-500 hover:text-indigo-600">24 avis</a>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-sm text-green-600 flex items-center">
                                <i class="fas fa-check-circle mr-1"></i> En stock
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-gray-900">25 DT</p>
                        <p class="text-sm text-gray-500">TVA incluse</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Description</h2>
                        <p class="text-gray-600 mb-4">
                            Notre bougie parfumée artisanale aux notes fraîches d'eucalyptus et de menthe apportera une
                            touche de fraîcheur et de sérénité à votre intérieur.
                            Fabriquée à la main avec de la cire de soja naturelle et des parfums de qualité.
                        </p>
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Durée de combustion : environ 50 heures</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Contenance : 300g</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Dimensions : 8cm de diamètre, 10cm de hauteur</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-indigo-600 mr-2 mt-1"></i>
                                <span>Fabriquée en Tunisie</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Color Variant -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Couleur</h3>
                        <div class="flex space-x-3">
                            <button
                                class="w-10 h-10 rounded-full bg-green-100 border-2 border-green-300 focus:ring-2 focus:ring-green-500"></button>
                            <button
                                class="w-10 h-10 rounded-full bg-blue-100 border-2 border-transparent hover:border-blue-300 focus:ring-2 focus:ring-blue-500"></button>
                            <button
                                class="w-10 h-10 rounded-full bg-gray-100 border-2 border-transparent hover:border-gray-300 focus:ring-2 focus:ring-gray-500"></button>
                        </div>
                    </div>

                    <!-- Quantity & Add to cart -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button class="px-3 py-2 text-gray-600 hover:text-indigo-600">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="px-3 py-1">1</span>
                                <button class="px-3 py-2 text-gray-600 hover:text-indigo-600">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-lg font-medium transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <i class="fas fa-shopping-cart mr-2"></i> Ajouter au panier
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
                        <a href="#" class="text-gray-500 hover:text-blue-500 mx-1">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-pink-500 mx-1">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-blue-400 mx-1">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-red-500 mx-1">
                            <i class="fab fa-pinterest-p"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Avis clients</h3>
                    <div class="flex items-center mb-6">
                        <div class="mr-4">
                            <span class="text-4xl font-bold">4.8</span>
                            <span class="text-gray-500">/5</span>
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                <div class="flex text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-sm text-gray-600">24 avis</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="mr-2">5 étoiles</span>
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 80%"></div>
                                </div>
                                <span>80%</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="mr-2">4 étoiles</span>
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                                <span>15%</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="mr-2">3 étoiles</span>
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 5%"></div>
                                </div>
                                <span>5%</span>
                            </div>
                        </div>
                    </div>
                    <button id="writeReviewBtn"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        aria-label="Écrire un avis">
                        <i class="fas fa-pen mr-2"></i> Écrire un avis
                    </button>
                </div>

                <!-- Formulaire d'avis (caché par défaut) -->
                <div id="reviewForm" class="hidden bg-gray-50 p-6 rounded-lg mb-6 transition-all duration-300">
                    <h4 class="text-lg font-semibold mb-4">Votre avis</h4>
                    <form id="reviewFormSubmit" class="space-y-4">
                        <!-- Étoiles pour la note -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Votre note</label>
                            <div class="flex space-x-1" role="radiogroup" aria-label="Note du produit">
                                <button type="button"
                                    class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                    data-rating="1" aria-label="1 étoile">
                                    <i class="fas fa-star text-xl"></i>
                                </button>
                                <button type="button"
                                    class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                    data-rating="2" aria-label="2 étoiles">
                                    <i class="fas fa-star text-xl"></i>
                                </button>
                                <button type="button"
                                    class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                    data-rating="3" aria-label="3 étoiles">
                                    <i class="fas fa-star text-xl"></i>
                                </button>
                                <button type="button"
                                    class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                    data-rating="4" aria-label="4 étoiles">
                                    <i class="fas fa-star text-xl"></i>
                                </button>
                                <button type="button"
                                    class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                    data-rating="5" aria-label="5 étoiles">
                                    <i class="fas fa-star text-xl"></i>
                                </button>
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                            <p id="ratingError" class="hidden text-red-500 text-sm mt-1">Veuillez sélectionner une
                                note.</p>
                        </div>
                        <!-- Commentaire -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700">Votre
                                commentaire</label>
                            <textarea id="comment" name="comment" rows="4"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Décrivez votre expérience avec ce produit..." required></textarea>
                            <p id="commentError" class="hidden text-red-500 text-sm mt-1">Veuillez entrer un
                                commentaire.</p>
                        </div>
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Votre nom
                                (optionnel)</label>
                            <input type="text" id="name" name="name"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Votre nom">
                        </div>
                        <!-- Localisation -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Votre localisation
                                (optionnel)</label>
                            <input type="text" id="location" name="location"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Votre ville">
                        </div>
                        <!-- Boutons -->
                        <div class="flex space-x-4">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Soumettre l'avis
                            </button>
                            <button type="button" id="cancelReview"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Annuler
                            </button>
                        </div>
                    </form>
                    <!-- Message de confirmation -->
                    <div id="successMessage" class="hidden text-green-600 text-sm mt-4">
                        Merci pour votre avis ! Il sera affiché après modération.
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Review 1 -->
                    <div class="border-b border-gray-200 pb-6">
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="text-sm text-gray-500">Il y a 2 jours</span>
                        </div>
                        <h4 class="font-semibold mb-1">Parfait pour ma salle de bain</h4>
                        <p class="text-gray-600 mb-2">
                            J'adore cette bougie ! Le parfum est frais sans être trop fort, parfait pour ma salle de
                            bain.
                            La durée de combustion est excellente et j'apprécie particulièrement qu'elle soit
                            fabriquée localement.
                        </p>
                        <div class="flex items-center">
                            <img src="https://randomuser.me/api/portraits/women/33.jpg" alt="Amina"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm font-medium">Amina, Tunis</span>
                        </div>
                    </div>
                    <!-- Review 2 -->
                    <div class="border-b border-gray-200 pb-6">
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="text-sm text-gray-500">Il y a 1 semaine</span>
                        </div>
                        <h4 class="font-semibold mb-1">Parfum délicieux</h4>
                        <p class="text-gray-600 mb-2">
                            Le parfum est vraiment agréable et se diffuse bien dans la pièce.
                            J'ai commandé plusieurs bougies pour offrir et tout le monde a adoré.
                            La livraison a été rapide et l'emballage soigné.
                        </p>
                        <div class="flex items-center">
                            <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Karim"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm font-medium">Karim, Sousse</span>
                        </div>
                    </div>
                    <!-- Review 3 -->
                    <div class="pb-6">
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-sm text-gray-500">Il y a 2 semaines</span>
                        </div>
                        <h4 class="font-semibold mb-1">Très satisfaite</h4>
                        <p class="text-gray-600 mb-2">
                            Belle bougie avec un parfum frais qui dure longtemps.
                            Le seul petit bémol est que la mèche a tendance à faire de la fumée si on ne la coupe
                            pas assez courte.
                        </p>
                        <div class="flex items-center">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Leila"
                                class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm font-medium">Leila, Sfax</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Related Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-6">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Related Product 1 -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <a href="/produits/bougie-lavande">
                        <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=500&q=80"
                            alt="Bougie Lavande" class="w-full h-48 object-cover" />
                    </a>
                    <div class="p-4">
                        <a href="/produits/bougie-lavande"
                            class="block font-medium text-gray-900 hover:text-indigo-600 mb-1">Bougie Lavande</a>
                        <p class="text-gray-600 text-sm mb-2">Parfum relaxant</p>
                        <p class="text-indigo-600 font-bold">22 DT</p>
                    </div>
                </div>
                <!-- Related Product 2 -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <a href="/produits/diffuseur-bambou">
                        <img src="https://images.unsplash.com/photo-1601001815894-4bb6c81416f9?auto=format&fit=crop&w=500&q=80"
                            alt="Diffuseur Bambou" class="w-full h-48 object-cover" />
                    </a>
                    <div class="p-4">
                        <a href="/produits/diffuseur-bambou"
                            class="block font-medium text-gray-900 hover:text-indigo-600 mb-1">Diffuseur Bambou</a>
                        <p class="text-gray-600 text-sm mb-2">Huiles essentielles</p>
                        <p class="text-indigo-600 font-bold">45 DT</p>
                    </div>
                </div>
                <!-- Related Product 3 -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <a href="/produits/set-de-2-bougies">
                        <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=500&q=80"
                            alt="Set de 2 bougies" class="w-full h-48 object-cover" />
                    </a>
                    <div class="p-4">
                        <a href="/produits/set-de-2-bougies"
                            class="block font-medium text-gray-900 hover:text-indigo-600 mb-1">Set de 2 bougies</a>
                        <p class="text-gray-600 text-sm mb-2">Parfum varié</p>
                        <p class="text-indigo-600 font-bold">40 DT</p>
                    </div>
                </div>
                <!-- Related Product 4 -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <a href="/produits/plateau-decoratif">
                        <img src="https://images.unsplash.com/photo-1598300052631-92d3f1f52a06?auto=format&fit=crop&w=500&q=80"
                            alt="Plateau décoratif" class="w-full h-48 object-cover" />
                    </a>
                    <div class="p-4">
                        <a href="/produits/plateau-decoratif"
                            class="block font-medium text-gray-900 hover:text-indigo-600 mb-1">Plateau décoratif</a>
                        <p class="text-gray-600 text-sm mb-2">Artisanat local</p>
                        <p class="text-indigo-600 font-bold">35 DT</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- VALEURS / AVANTAGES -->
    <section class="container mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-12 justify-center text-center">
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
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
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-indigo-600 mb-2">Satisfait ou Remboursé</h3>
                <p class="text-gray-600">Achetez sereinement</p>
            </div>
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 20c4 0 7-3 7-7V6"></path>
                        <path d="M5 9V6a7 7 0 0 1 14 0v3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-indigo-600 mb-2">Décorations Artisanales</h3>
                <p class="text-gray-600">Faites à la main, sélection locale</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-100 py-16 border-t border-gray-200">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 px-6 text-sm text-gray-700">
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Paradis Déco</h4>
                <p class="text-xs text-gray-500">L’élégance contemporaine pour votre maison : accessoires, meubles,
                    éclairages sélectionnés avec soin.</p>
                <div class="flex space-x-4 mt-4">
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook"
                        class="text-gray-600 hover:text-indigo-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 12.073C22 6.507 17.523 2 12 2S2 6.507 2 12.073c0 4.986 3.657 9.128 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.845c0-2.508 1.493-3.89 3.777-3.89 1.094 0 2.238 .196 2.238 .196v2.46h-1.26c-1.243 0-1.63 .771-1.63 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.201 22 17.059 22 12.073z" />
                        </svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram"
                        class="text-gray-600 hover:text-indigo-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M7.75 2h8.5C19.821 2 22 4.179 22 7.75v8.5C22 19.821 19.821 22 16.25 22h-8.5C4.179 22 2 19.821 2 16.25v-8.5C2 4.179 4.179 2 7.75 2zm0 2C5.679 4 4 5.679 4 7.75v8.5C4 18.321 5.679 20 7.75 20h8.5c2.071 0 3.75-1.679 3.75-3.75v-8.5C20 5.679 18.321 4 16.25 4h-8.5zm8.75 1.75a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0zm-4.5 1.5a5.5 5.5 0 110 11 5.5 5.5 0 010-11zm0 2C10.672 9.75 9.75 10.672 9.75 12s.922 2.25 2.25 2.25 2.25-.922 2.25-2.25S13.328 9.75 12 9.75z" />
                        </svg>
                    </a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter"
                        class="text-gray-600 hover:text-indigo-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22.162 5.656c-.814 .363-1.688 .607-2.606 .717a4.548 4.548 0 001.992-2.506 9.055 9.055 0 01-2.885 1.104 4.523 4.523 0 00-7.697 4.123 12.833 12.833 0 01-9.316-4.725 4.526 4.526 0 001.402 6.044 4.502 4.502 0 01-2.049-.566v.057a4.527 4.527 0 003.632 4.439 4.536 4.536 0 01-2.042 .078 4.529 4.529 a 4.529 0 004.227 3.141 9.074 9.074 0 01-5.596 1.928 9.293 9.293 0 01-1.077-.063 12.79 12.79 0 006.914 2.027c8.295 0 12.83-6.872 12.83-12.83 0-.196-.004-.393-.013-.588a9.174 9.174 0 002.257-2.339z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Collections</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600 transition">Packs</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Nouveautés</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Offres exclusives</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Informations</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-indigo-600 transition">Contact</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">FAQ</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition">Politique de retour</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Newsletter</h4>
                <form class="flex flex-col space-y-3">
                    <input type="email" class="border border-gray-300 px-4 py-2 rounded-full text-sm"
                        placeholder="Votre email" />
                    <button
                        class="bg-indigo-600 text-white px-6 py-2 rounded-full text-sm hover:bg-indigo-700 transition">
                        S'inscrire
                    </button>
                </form>
            </div>
        </div>
        <div class="text-center text-xs text-gray-400 mt-10">© {{ date('Y') }} Paradis Déco. Tous droits
            réservés.</div>
    </footer>

    <!-- JavaScript pour la Galerie et le Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Gestion de la galerie d'images
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('.flex.space-x-3 img');
            const prevButton = document.getElementById('prevImage');
            const nextButton = document.getElementById('nextImage');
            let currentIndex = 0;
            const images = Array.from(thumbnails).map(img => img.src);

            // Préchargement des images
            images.forEach(src => {
                const img = new Image();
                img.src = src;
            });

            // Changer l'image principale
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

            // Gestion des clics sur les miniatures
            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => changeImage(index));
            });

            // Navigation avec flèches
            prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
            nextButton.addEventListener('click', () => changeImage(currentIndex + 1));

            // Gestion des swipes pour mobile
            let touchStartX = 0;
            let touchEndX = 0;
            mainImage.parentElement.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            mainImage.parentElement.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) changeImage(currentIndex + 1); // Swipe gauche
                if (touchEndX - touchStartX > 50) changeImage(currentIndex - 1); // Swipe droite
            });

            // Gestion du modal du panier
            const cartModal = document.getElementById('cartModal');
            const openCartButtons = document.querySelectorAll('[aria-label="Panier"], [href="/panier"]');
            const closeCartModal = document.getElementById('closeCartModal');
            const cartModalBackdrop = document.getElementById('cartModalBackdrop');
            const continueShopping = document.getElementById('continueShopping');

            const closeModal = () => {
                cartModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            openCartButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    cartModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });

            closeCartModal.addEventListener('click', closeModal);
            cartModalBackdrop.addEventListener('click', closeModal);
            continueShopping.addEventListener('click', closeModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !cartModal.classList.contains('hidden')) closeModal();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Gestion de la galerie d'images
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('.flex.space-x-3 img');
            const prevButton = document.getElementById('prevImage');
            const nextButton = document.getElementById('nextImage');
            let currentIndex = 0;
            const images = Array.from(thumbnails).map(img => img.src);

            // Préchargement des images
            images.forEach(src => {
                const img = new Image();
                img.src = src;
            });

            // Changer l'image principale
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

            // Gestion des clics sur les miniatures
            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => changeImage(index));
            });

            // Navigation avec flèches
            prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
            nextButton.addEventListener('click', () => changeImage(currentIndex + 1));

            // Gestion des swipes pour mobile
            let touchStartX = 0;
            let touchEndX = 0;
            mainImage.parentElement.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            mainImage.parentElement.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) changeImage(currentIndex + 1); // Swipe gauche
                if (touchEndX - touchStartX > 50) changeImage(currentIndex - 1); // Swipe droite
            });

            // Gestion du modal du panier
            const cartModal = document.getElementById('cartModal');
            const openCartButtons = document.querySelectorAll('[aria-label="Panier"], [href="/panier"]');
            const closeCartModal = document.getElementById('closeCartModal');
            const cartModalBackdrop = document.getElementById('cartModalBackdrop');
            const continueShopping = document.getElementById('continueShopping');

            const closeModal = () => {
                cartModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            openCartButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    cartModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });

            closeCartModal.addEventListener('click', closeModal);
            cartModalBackdrop.addEventListener('click', closeModal);
            continueShopping.addEventListener('click', closeModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !cartModal.classList.contains('hidden')) closeModal();
            });

            // Gestion du formulaire d'avis
            const writeReviewBtn = document.getElementById('writeReviewBtn');
            const reviewForm = document.getElementById('reviewForm');
            const reviewFormSubmit = document.getElementById('reviewFormSubmit');
            const cancelReview = document.getElementById('cancelReview');
            const starButtons = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating');
            const commentInput = document.getElementById('comment');
            const ratingError = document.getElementById('ratingError');
            const commentError = document.getElementById('commentError');
            const successMessage = document.getElementById('successMessage');

            // Afficher/masquer le formulaire
            writeReviewBtn.addEventListener('click', () => {
                reviewForm.classList.toggle('hidden');
                if (!reviewForm.classList.contains('hidden')) {
                    reviewForm.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });

            // Annuler le formulaire
            cancelReview.addEventListener('click', () => {
                reviewForm.classList.add('hidden');
                reviewFormSubmit.reset();
                starButtons.forEach(btn => btn.classList.remove('text-yellow-400'));
                ratingInput.value = '';
                ratingError.classList.add('hidden');
                commentError.classList.add('hidden');
                successMessage.classList.add('hidden');
            });

            // Gestion des étoiles
            starButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const rating = button.getAttribute('data-rating');
                    ratingInput.value = rating;
                    starButtons.forEach(btn => {
                        const btnRating = btn.getAttribute('data-rating');
                        btn.classList.toggle('text-yellow-400', btnRating <= rating);
                        btn.classList.toggle('text-gray-300', btnRating > rating);
                    });
                    ratingError.classList.add('hidden');
                });
            });

            // Validation et soumission du formulaire
            reviewFormSubmit.addEventListener('submit', (e) => {
                e.preventDefault();
                let isValid = true;

                // Vérifier la note
                if (!ratingInput.value) {
                    ratingError.classList.remove('hidden');
                    isValid = false;
                } else {
                    ratingError.classList.add('hidden');
                }

                // Vérifier le commentaire
                if (!commentInput.value.trim()) {
                    commentError.classList.remove('hidden');
                    isValid = false;
                } else {
                    commentError.classList.add('hidden');
                }

                if (isValid) {
                    // Simuler la soumission (remplacez ceci par une requête API si nécessaire)
                    console.log({
                        rating: ratingInput.value,
                        comment: commentInput.value,
                        name: document.getElementById('name').value,
                        location: document.getElementById('location').value
                    });
                    reviewForm.classList.add('hidden');
                    successMessage.classList.remove('hidden');
                    reviewFormSubmit.reset();
                    starButtons.forEach(btn => btn.classList.remove('text-yellow-400'));
                    ratingInput.value = '';
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 5000); // Masquer le message après 5 secondes
                }
            });
        });
    </script>
</body>

</html>
