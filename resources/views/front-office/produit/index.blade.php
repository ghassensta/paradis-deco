@extends('front-office.layouts.app')
@section('title', $product->meta_title)
@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta name="theme-color" content="#FDD835">
    <meta name="description"
        content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
    <meta property="og:description"
        content="{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('storage/' . ($product->images[0] ?? 'default.jpg')) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
    <meta property="product:price:amount" content="{{ number_format($product->price, 2) }}">
    <meta property="product:price:currency" content="TND">
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-yellow-600">Accueil</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ route('allproduits') }}" class="text-gray-500 hover:text-yellow-600">Tous les
                            produits</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ url()->current() }}" class="text-gray-500 text-yellow-600">{{ $product->name }}</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="lg:sticky lg:top-4">
                <div class="relative mb-4 bg-white rounded-xl shadow-sm overflow-hidden group">
                    <img id="mainImage" src="{{ Storage::url($product->images[0] ?? 'default.jpg') }}"
                        alt="{{ $product->name }} - image principale"
                        class="w-full h-96 object-contain main-image transform group-hover:scale-105 transition-transform duration-300" />
                    <button id="prevImage"
                        class="gallery-nav absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md opacity-0 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        aria-label="Image précédente">
                        <i class="fas fa-chevron-left text-gray-700"></i>
                    </button>
                    <button id="nextImage"
                        class="gallery-nav absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md opacity-0 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        aria-label="Image suivante">
                        <i class="fas fa-chevron-right text-gray-700"></i>
                    </button>
                </div>
                <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
                    @foreach ($product->images as $index => $image)
                        <img src="{{ Storage::url($image) }}" alt="{{ $product->name }} - image {{ $index + 1 }}"
                            class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent hover:border-yellow-600 transform transition-all duration-300 hover:scale-105"
                            data-index="{{ $index }}" />
                    @endforeach
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="mb-4">
                        @if ($product->created_at->diffInDays(now()) < 10)
                            <span
                                class="inline-block bg-yellow-100 text-yellow-600 text-xs font-semibold px-2 py-1 rounded mb-2">Nouveauté</span>
                        @endif
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <a href="#reviews" class="text-sm text-gray-500 hover:text-yellow-600">24 avis</a>
                            <span class="mx-2 text-gray-300">|</span>
                            @if ($product->stock > 0)
                                <span class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> En stock
                                </span>
                            @else
                                <span class="flex items-center text-sm text-red-600">
                                    <i class="fas fa-times-circle mr-1"></i> Rupture de stock
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DT</p>
                        <p class="text-sm text-gray-500">TVA incluse</p>
                    </div>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Description</h2>
                        <p class="text-gray-600">{{ $product->description }}</p>
                    </div>
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            {{-- <div class="flex items-center border border-gray-300 rounded-lg">
                                <button
                                    class="px-3 py-2 text-gray-600 hover:text-yellow-600 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="updateQuantity(-1, {{ $product->id }}, {{ $product->stock }})"
                                    aria-label="Diminuer la quantité" {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantity" class="px-3 py-1">1</span>
                                <button
                                    class="px-3 py-2 text-gray-600 hover:text-yellow-600 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="updateQuantity(1, {{ $product->id }}, {{ $product->stock }})"
                                    aria-label="Augmenter la quantité" {{ $product->stock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div> --}}
                            <button
                                class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-6 rounded-lg font-medium transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-yellow-500 {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ Storage::url($product->images[0] ?? 'default.jpg') }}"
                                data-stock="{{ $product->stock }}" onclick="addToCart(this)"
                                aria-label="Ajouter {{ $product->name }} au panier"
                                {{ $product->stock == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart mr-2"></i> Ajouter au panier
                                <svg class="animate-spin h-5 w-5 text-white hidden ml-2" id="addToCartSpinner"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-yellow-600 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-truck text-yellow-600 mr-3 mt-1"></i>
                            <div>
                                <h3 class="font-semibold mb-1">Livraison rapide</h3>
                                <p class="text-sm text-gray-600">
                                    Livraison en 24-48h à Tunis et 2-3 jours pour le reste du pays.
                                    <a href="/a-propos" class="text-yellow-600 hover:underline">En savoir plus</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center pt-4 border-t border-gray-200">
                        <span class="text-sm text-gray-600 mr-3">Partager :</span>
                        <a href="https://www.facebook.com/paradisdeco123?rdid=uEzMuF39ptOZCBY5&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1EDbexpcnp%2F#" target="__blank" class="text-gray-500 hover:text-blue-500 mx-1">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/paradisdeco_?igsh=MWpsdzRwNWF3cDIxaw%3D%3D" target="__blank" class="text-gray-500 hover:text-pink-500 mx-1">
                            <i class="fab fa-instagram"></i>
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
                            <span class="text-4xl font-bold">{{ $averageRating }}</span>
                            <span class="text-gray-500">/5</span>
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                <div class="flex text-yellow-400 mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star{{ $i <= floor($averageRating) ? '' : ($i <= $averageRating ? '-half-alt' : '') }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">{{ $totalReviews }} avis</span>
                            </div>
                            @foreach ([5, 4, 3, 2, 1] as $rating)
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="mr-2">{{ $rating }} étoile{{ $rating > 1 ? 's' : '' }}</span>
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-400 h-2 rounded-full"
                                            style="width: {{ $ratingDistribution[$rating] }}%"></div>
                                    </div>
                                    <span>{{ number_format($ratingDistribution[$rating], 0) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button id="writeReviewBtn"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        aria-label="Écrire un avis">
                        <i class="fas fa-pen mr-2"></i> Écrire un avis
                    </button>
                </div>
                <div id="reviewForm" class="hidden bg-gray-50 p-6 rounded-lg mb-6 transition-all duration-300">
                    <h4 class="text-lg font-semibold mb-4">Votre avis</h4>
                    <form id="reviewFormSubmit" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Votre note</label>
                            <div class="flex space-x-1" role="radiogroup" aria-label="Note du produit">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                        class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none"
                                        data-rating="{{ $i }}"
                                        aria-label="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                                        <i class="fas fa-star text-xl"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                            <p id="ratingError" class="hidden text-red-500 text-sm mt-1">Veuillez sélectionner une note.
                            </p>
                        </div>
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700">Votre
                                commentaire</label>
                            <textarea id="comment" name="comment" rows="4"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="Décrivez votre expérience avec ce produit..." required></textarea>
                            <p id="commentError" class="hidden text-red-500 text-sm mt-1">Veuillez entrer un commentaire.
                            </p>
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Votre nom
                                (optionnel)</label>
                            <input type="text" id="name" name="name"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="Votre nom">
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Votre localisation
                                (optionnel)</label>
                            <input type="text" id="location" name="location"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="Votre ville">
                        </div>
                        <div class="flex space-x-4">
                            <button type="submit"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                Soumettre l'avis
                            </button>
                            <button type="button" id="cancelReview"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Annuler
                            </button>
                        </div>
                    </form>
                    <div id="successMessage" class="hidden text-green-600 text-sm mt-4">
                        Merci pour votre avis ! Il sera affiché après modération.
                    </div>
                    <div id="errorMessage" class="hidden text-red-600 text-sm mt-4"></div>
                </div>
                <div class="space-y-6">
                    @forelse ($reviews as $review)
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center mb-3">
                                <div class="flex text-yellow-400 mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-semibold mb-1">{{ $review->comment }}</h4>
                            <div class="flex items-center">
                                <!-- Placeholder image, replace with actual user image if available -->
                                <img src="https://i.pravatar.cc/128?img={{ rand(1, 70) }}" alt="{{ $review->name }}"
                                    class="w-8 h-8 rounded-full mr-2">

                                <span class="text-sm font-medium">
                                    {{ $review->name }}{{ $review->location ? ', ' . $review->location : '' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600">Aucun avis pour ce produit pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>


        <!-- Related Products -->
        <div class="mt-16 px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-8 text-gray-800">Vous aimerez aussi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
                @forelse ($similarProducts as $item)
                    @if ($item->is_active)
                        <article
                            class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
                            itemscope itemtype="http://schema.org/Product">
                            <div class="relative overflow-hidden flex-grow">
                                <a href="{{ route('preview-article', $item->slug) }}" target="_blank"
                                    title="{{ $item->meta_title ?? $item->name }}" class="block h-full">
                                    <img src="{{ asset('storage/' . ($item->images[0] ?? 'default.jpg')) }}"
                                        alt="{{ $item->name }}"
                                        class="h-48 sm:h-56 w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out"
                                        loading="lazy" itemprop="image" />
                                </a>
                                @if ($item->created_at->diffInDays(now()) < 10)
                                    <div class="absolute top-3 right-3">
                                        <span
                                            class="bg-[#228B22] text-white text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wide">Nouveau</span>
                                    </div>
                                @endif
                                @if ($item->stock <= 5 && $item->stock > 0)
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wide">Stock
                                            faible</span>
                                    </div>
                                @elseif ($item->stock == 0)
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="bg-gray-600 text-white text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wide">Épuisé</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800 hover:text-yellow-600 transition-colors line-clamp-2"
                                            itemprop="name">
                                            <a href="{{ route('preview-article', $item->slug) }}">{{ $item->name }}</a>
                                        </h3>
                                        <div class="flex items-center ml-2" itemprop="aggregateRating" itemscope
                                            itemtype="http://schema.org/AggregateRating">
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                            <span class="text-gray-600 text-xs ml-1" itemprop="ratingValue">4.8</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                        {{ Str::limit($item->description, 60) }}</p>
                                </div>
                                <div class="mt-auto">
                                    <div class="flex justify-between items-center">
                                        <p class="text-black font-bold text-lg" itemprop="offers" itemscope
                                            itemtype="http://schema.org/Offer">
                                            <span itemprop="price">{{ number_format($item->price, 2) }}</span> DT
                                            <meta itemprop="priceCurrency" content="TND">
                                        </p>
                                        <button aria-label="Ajouter {{ $item->name }} au panier"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-price="{{ $item->price }}"
                                            data-image="{{ asset('storage/' . ($item->images[0] ?? 'default.jpg')) }}"
                                            data-stock="{{ $item->stock }}"
                                            class="cursor-pointer flex items-center justify-center gap-1 bg-black hover:bg-gray-800 text-white px-3 py-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transform hover:scale-[1.03] active:scale-95 text-sm sm:text-base"
                                            @if ($item->stock == 0) disabled @endif onclick="addToCart(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span class="hidden sm:inline">Ajouter</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <meta itemprop="description"
                                content="{{ $item->meta_description ?? Str::limit($item->description, 80) }}">
                            <meta itemprop="url" content="{{ route('preview-article', $item->slug) }}">
                        </article>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg mb-4">Aucun produit disponible pour le moment.</p>
                        <a href="{{ route('allproduits') }}"
                            class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300">
                            Voir tous les produits
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Values / Advantages -->
    <section class="container mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-12 justify-center text-center">
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-black/10 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                        <path d="M16 8h2l3 3v5a2 2 0 01-2 2h-1"></path>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-black mb-2">Livraison Express</h3>
                <p class="text-gray-600">Partout en Tunisie sous 48h</p>
            </div>
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-black/10 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-black mb-2">Satisfait ou Remboursé</h3>
                <p class="text-gray-600">Achetez sereinement</p>
            </div>
            <div>
                <div class="mx-auto mb-4 w-12 h-12 bg-black/10 flex items-center justify-center rounded-full">
                    <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 20c4 0 7-3 7-7V6"></path>
                        <path d="M5 9V6a7 7 0 0 1 14 0v3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-black mb-2">Décorations Artisanales</h3>
                <p class="text-gray-600">Faites à la main, sélection locale</p>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
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

        .gallery-nav {
            transition: opacity 0.3s ease;
        }

        .gallery-nav:hover {
            opacity: 1;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        button.loading #addToCartSpinner {
            display: inline-block;
        }

        button.loading {
            background-color: #a5b4fc;
            cursor: wait;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gallery Management
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('.flex.space-x-3 img');
            const prevButton = document.getElementById('prevImage');
            const nextButton = document.getElementById('nextImage');
            let currentIndex = 0;
            const images = Array.from(thumbnails).map(img => img.src);

            images.forEach(src => {
                const img = new Image();
                img.src = src;
            });

            const changeImage = (index) => {
                if (index < 0) index = images.length - 1;
                if (index >= images.length) index = 0;
                mainImage.classList.add('fade-out');
                setTimeout(() => {
                    mainImage.src = images[index];
                    mainImage.classList.remove('fade-out');
                    mainImage.classList.add('fade-in');
                    thumbnails.forEach(thumb => thumb.classList.remove('border-yellow-600', 'active'));
                    thumbnails[index].classList.add('border-yellow-600', 'active');
                    currentIndex = index;
                }, 300);
            };

            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => changeImage(index));
            });

            prevButton.addEventListener('click', () => changeImage(currentIndex - 1));
            nextButton.addEventListener('click', () => changeImage(currentIndex + 1));

            let touchStartX = 0;
            let touchEndX = 0;
            mainImage.parentElement.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            mainImage.parentElement.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) changeImage(currentIndex + 1);
                if (touchEndX - touchStartX > 50) changeImage(currentIndex - 1);
            });

            // Review Form Management
            const writeReviewBtn = document.getElementById('writeReviewBtn');
            const reviewForm = document.getElementById('reviewForm');
            const cancelReviewBtn = document.getElementById('cancelReview');
            const reviewFormSubmit = document.getElementById('reviewFormSubmit');
            const ratingButtons = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating');
            const ratingError = document.getElementById('ratingError');
            const commentError = document.getElementById('commentError');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            // Debugging: Check if elements are found
            if (!writeReviewBtn || !reviewForm || !cancelReviewBtn || !reviewFormSubmit) {
                console.error('Review form elements not found:', {
                    writeReviewBtn: !!writeReviewBtn,
                    reviewForm: !!reviewForm,
                    cancelReviewBtn: !!cancelReviewBtn,
                    reviewFormSubmit: !!reviewFormSubmit
                });
                return;
            }

            // Toggle review form visibility
            writeReviewBtn.addEventListener('click', function() {
                console.log('Write review button clicked');
                reviewForm.classList.toggle('hidden');
                if (!reviewForm.classList.contains('hidden')) {
                    reviewForm.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });

            // Cancel review form
            cancelReviewBtn.addEventListener('click', function() {
                console.log('Cancel review button clicked');
                reviewForm.classList.add('hidden');
                reviewFormSubmit.reset();
                ratingButtons.forEach(btn => btn.classList.remove('text-yellow-400'));
                ratingInput.value = '';
                ratingError.classList.add('hidden');
                commentError.classList.add('hidden');
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
            });

            // Handle star rating selection
            ratingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    console.log('Star rating selected:', rating);
                    ratingInput.value = rating;
                    ratingButtons.forEach(btn => {
                        btn.classList.toggle('text-yellow-400', btn.getAttribute(
                            'data-rating') <= rating);
                        btn.classList.toggle('text-gray-300', btn.getAttribute(
                            'data-rating') > rating);
                    });
                    ratingError.classList.add('hidden');
                });
            });

            // Handle form submission
            reviewFormSubmit.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Review form submitted');

                if (!ratingInput.value) {
                    ratingError.classList.remove('hidden');
                    return;
                }

                if (!document.getElementById('comment').value.trim()) {
                    commentError.classList.remove('hidden');
                    return;
                }

                const formData = new FormData(reviewFormSubmit);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('avis.storeReview') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            successMessage.classList.remove('hidden');
                            errorMessage.classList.add('hidden');
                            reviewFormSubmit.reset();
                            ratingButtons.forEach(btn => btn.classList.remove('text-yellow-400'));
                            ratingInput.value = '';
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });
                            setTimeout(() => {
                                reviewForm.classList.add('hidden');
                                successMessage.classList.add('hidden');
                            }, 2000);
                        } else {
                            errorMessage.classList.remove('hidden');
                            errorMessage.textContent = data.errors ? Object.values(data.errors).flat()
                                .join(' ') : 'Une erreur est survenue.';
                            successMessage.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        errorMessage.classList.remove('hidden');
                        errorMessage.textContent =
                            'Une erreur est survenue lors de l\'envoi de l\'avis.';
                        successMessage.classList.add('hidden');
                    });
            });

            // Quantity and Cart Management
            const quantityDisplay = document.getElementById('quantity');
            const productId = {{ $product->id }};
            const productStock = {{ $product->stock }};

            function initializeQuantity() {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const existingItem = cart.find(item => item.id === productId);
                quantityDisplay.textContent = existingItem ? existingItem.quantity : 1;
            }

            window.updateQuantity = (change, id, stock) => {
                let currentQuantity = parseInt(quantityDisplay.textContent);
                currentQuantity += change;

                if (currentQuantity < 1) {
                    currentQuantity = 1;
                    showNotification('La quantité minimale est 1.');
                } else if (currentQuantity > stock) {
                    currentQuantity = stock;
                    showNotification('Stock maximum atteint.', 'error');
                }

                quantityDisplay.textContent = currentQuantity;
            };

            window.addToCart = function(button) {
                button.classList.add('loading');
                button.disabled = true;
                const spinner = button.querySelector('#addToCartSpinner');
                spinner.classList.remove('hidden');

                try {
                    const product = {
                        id: parseInt(button.dataset.id),
                        name: button.dataset.name,
                        price: parseFloat(button.dataset.price),
                        image: button.dataset.image,
                        stock: parseInt(button.dataset.stock),
                        quantity: parseInt(quantityDisplay.textContent)
                    };

                    if (!product.id || !product.name || isNaN(product.price) || !product.image || isNaN(product
                            .stock)) {
                        throw new Error('Données du produit invalides.');
                    }
                    if (product.stock === 0) {
                        throw new Error('Produit épuisé.');
                    }

                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    cart = cart.filter(i => i.id && i.name && !isNaN(i.price) && i.image && !isNaN(i.stock));
                    const existingItem = cart.find(item => item.id === product.id);

                    if (existingItem) {
                        existingItem.quantity = product.quantity;
                        if (existingItem.quantity > product.stock) {
                            existingItem.quantity = product.stock;
                            quantityDisplay.textContent = product.stock;
                            showNotification('Stock maximum atteint.', 'error');
                        }
                    } else {
                        cart.push(product);
                    }

                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartCount();
                    updateCartUI();
                    openCartOffcanvas();
                    showNotification(
                        `${product.name} ajouté au panier avec ${product.quantity} unité${product.quantity > 1 ? 's' : ''}.`
                    );
                } catch (err) {
                    showNotification(err.message, 'error');
                    console.error(err);
                } finally {
                    button.classList.remove('loading');
                    button.disabled = false;
                    spinner.classList.add('hidden');
                }
            };

            function showNotification(message, type = 'success') {
                const toast = document.createElement('div');
                toast.classList.add('fixed', 'top-4', 'right-4', 'p-4', 'rounded-lg', 'shadow-lg', 'text-white',
                    'text-sm', 'transition-opacity', 'duration-300', 'z-50');
                toast.classList.add(type === 'success' ? 'bg-green-600' : 'bg-red-600');
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            function updateCartCount() {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                const cartCountElement = document.getElementById('cartCount');
                if (cartCountElement) {
                    cartCountElement.textContent = count;
                }
            }

            function updateCartUI() {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const cartItems = document.getElementById('cartItems');
                if (cartItems) {
                    if (cart.length === 0) {
                        cartItems.innerHTML = '<p class="text-gray-600 text-center">Votre panier est vide.</p>';
                    } else {
                        cartItems.innerHTML = cart.map(item => `
                            <div class="flex items-center p-2 border-b">
                                <img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded-md mr-3">
                                <div class="flex-1">
                                    <p class="text-sm font-medium">${item.name}</p>
                                    <p class="text-xs text-gray-600">${item.price.toFixed(2)} DT x ${item.quantity}</p>
                                </div>
                                <button class="text-red-600" onclick="removeFromCart(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `).join('');
                    }
                }
            }

            initializeQuantity();
            updateCartCount();
            updateCartUI();
        });
    </script>
@endsection
