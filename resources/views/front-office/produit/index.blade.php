@extends('front-office.layouts.app')

@php
    $variants = $product->storefrontVariants();
    // Variante présélectionnée : la première disponible, pour que le prix et
    // l'image affichés correspondent à ce qui part au panier dès le chargement.
    $selectedVariant = collect($variants)->first(fn ($v) => $v['stock'] > 0) ?? ($variants[0] ?? null);
    $displayPrice = $selectedVariant['price'] ?? $product->price;
    $displayStock = $selectedVariant['stock'] ?? $product->stock;
    $displayImage = $selectedVariant['image'] ?? $product->cover_url;
@endphp
@section('description', $product->seo_description)
@section('title', $product->seo_title)
@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta name="theme-color" content="#FDD835">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
        <meta name="keywords" content="{{
    isset($product->meta_keywords)
        ? (is_array($product->meta_keywords)
            ? implode(',', $product->meta_keywords)
            : $product->meta_keywords)
        : ''
}}">

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
    <meta property="product:price:amount" content="{{ number_format($product->price, 2) }}">
    <meta property="product:price:currency" content="TND">
    @php
        // Données structurées construites via json_encode : échappement garanti,
        // aucune clé inventée, et les champs absents sont simplement omis.
        $schemaImages = collect([$product->image_avant])
            ->merge($images)
            ->filter()
            ->map(fn ($path) => \App\Models\Product::mediaUrl($path))
            ->values();

        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            // Priorité : résumé dédié, sinon description longue. RichText::toText
            // nettoie le HTML avant extraction : aucun balisage ni code résiduel.
            'description' => Str::limit(
                \App\Support\RichText::toText($product->description_courte ?: $product->description ?: $product->name),
                300
            ),
            'sku' => $product->sku ?: (string) $product->id,
            'brand' => ['@type' => 'Brand', 'name' => config('app.name')],
            'url' => route('preview-article', $product->slug),
            'offers' => [
                '@type' => 'Offer',
                'url' => route('preview-article', $product->slug),
                'priceCurrency' => 'TND',
                'price' => number_format((float) $product->price, 2, '.', ''),
                'availability' => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];

        // Une offre par variante : prix et disponibilité réels de chaque option.
        if ($variants !== []) {
            $productSchema['offers'] = collect($variants)->map(fn ($variant) => [
                '@type' => 'Offer',
                'name' => $variant['label'],
                'url' => route('preview-article', ['slug' => $product->slug, 'variant' => $variant['id']]),
                'image' => $variant['image'],
                'priceCurrency' => 'TND',
                'price' => number_format($variant['price'], 2, '.', ''),
                'availability' => $variant['stock'] > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ])->all();
        }

        if ($schemaImages->isNotEmpty()) {
            $productSchema['image'] = $schemaImages->all();
        }

        // Aucune note n'est déclarée tant qu'aucun avis approuvé n'existe.
        if ($totalReviews > 0) {
            $productSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format((float) $averageRating, 1, '.', ''),
                'reviewCount' => $totalReviews,
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

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


@endsection
@section('og_image', $product->cover_url)

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex text-sm" aria-label="Fil d'Ariane">
                <ol class="flex flex-wrap items-center gap-x-2">
                    @foreach ($breadcrumbs as $crumb)
                        @unless ($loop->first)
                            <li class="text-gray-400" aria-hidden="true">/</li>
                        @endunless
                        <li>
                            @if ($loop->last)
                                <span class="text-yellow-600 font-medium" aria-current="page">{{ $crumb['name'] }}</span>
                            @else
                                <a href="{{ $crumb['url'] }}" class="text-gray-500 hover:text-yellow-600">{{ $crumb['name'] }}</a>
                            @endif
                        </li>
                    @endforeach
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
                    <img id="mainImage" src="{{ $displayImage }}"
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
                @php
                $images = [];
                if (!empty($product->images)) {
                    if (is_array($product->images)) {
                        $images = $product->images;
                    } else {
                        $decoded = json_decode($product->images, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $images = $decoded;
                        }
                    }
                }
            @endphp

            <div class="flex space-x-3 overflow-x-auto py-2 scrollbar-hide">
                @forelse ($images as $index => $image)
                    <img src="{{ Storage::url($image) }}"
                        alt="{{ $product->name }} - image {{ $index + 1 }}"
                        loading="lazy"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent hover:border-yellow-600 transform transition-all duration-300 hover:scale-105"
                        data-index="{{ $index }}" />
                @empty
                    <img src="{{ asset('images/no-image.png') }}"
                        alt="Image par défaut"
                        class="w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent hover:border-yellow-600 transform transition-all duration-300 hover:scale-105" />
                @endforelse
            </div>

            </div>

            <!-- Product Info -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                   <div class="mb-4">
    {{-- Badge nouveauté (moins de 10 jours) --}}
    @if ($product->created_at && $product->created_at->diffInDays(now()) < 10)
        <span class="inline-block bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded mb-2">
            Nouveauté
        </span>
    @endif

    {{-- Nom du produit --}}
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 leading-snug">
        {{ $product->name }}
    </h1>

    {{-- Étoiles + avis + stock --}}
    <div class="flex items-center flex-wrap gap-2 mb-2">
        {{-- Note moyenne --}}
        <div class="flex items-center text-yellow-400">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= floor($averageRating))
                    <i class="fas fa-star"></i>
                @elseif ($i - $averageRating < 1)
                    <i class="fas fa-star-half-alt"></i>
                @else
                    <i class="far fa-star"></i>
                @endif
            @endfor
        </div>

        {{-- Nombre d’avis --}}
        <a href="#reviews" class="text-sm text-gray-500 hover:text-yellow-600">
            {{ $totalReviews }} avis
        </a>

        {{-- Séparateur --}}
        <span class="mx-2 text-gray-300">|</span>

        {{-- Stock --}}
        <span id="stockInStock" class="{{ $displayStock > 0 ? 'flex' : 'hidden' }} items-center text-sm text-green-600">
            <i class="fas fa-check-circle mr-1"></i> En stock
        </span>
        <span id="stockOut" class="{{ $displayStock > 0 ? 'hidden' : 'flex' }} items-center text-sm text-red-600">
            <i class="fas fa-times-circle mr-1"></i> Rupture de stock
        </span>
    </div>
</div>

                    <div class="mb-6">
                        <p class="text-3xl font-bold text-gray-900"><span id="productPrice">{{ number_format($displayPrice, 2) }}</span> DT</p>
                        <p class="text-sm text-gray-500">TVA incluse</p>
                        @php
                            $shippingFee = (float) ($config->shipping_cost ?? 0);
                            $freeFrom = (float) ($config->free_shipping_threshold ?? 0);
                            $dt = fn ($v) => number_format($v, fmod($v, 1) ? 2 : 0, ',', ' ') . ' DT';
                        @endphp
                        <p class="mt-1 text-sm text-gray-600">
                            @if ($shippingFee > 0)
                                + {{ $dt($shippingFee) }} de livraison
                                @if ($freeFrom > 0)
                                    · <strong>offerte dès {{ $dt($freeFrom) }}</strong>
                                @endif
                            @else
                                <strong>Livraison offerte</strong>
                            @endif
                        </p>
                    </div>
                    <div class="mb-6">
                        {{-- Résumé : le détail complet vit dans sa propre section plus bas. --}}
                        @if (filled($product->short_description))
                            <p class="text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
                        @endif
                    </div>
                    @if ($variants !== [])
                        <div id="variantPicker" class="mb-6 space-y-4">
                            @foreach (['type' => 'Type', 'size' => 'Taille'] as $axis => $axisLabel)
                                @php $axisValues = collect($variants)->pluck($axis)->filter()->unique()->values(); @endphp
                                @if ($axisValues->isNotEmpty())
                                    <fieldset>
                                        <legend class="mb-2 text-sm font-medium text-gray-900">
                                            {{ $axisLabel }} :
                                            <span class="font-normal text-gray-600" data-axis-selected="{{ $axis }}">{{ $selectedVariant[$axis] }}</span>
                                        </legend>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($axisValues as $value)
                                                <button type="button" data-axis="{{ $axis }}" data-value="{{ $value }}"
                                                    aria-pressed="{{ $selectedVariant[$axis] === $value ? 'true' : 'false' }}"
                                                    class="variant-option rounded-lg border px-4 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                    {{ $value }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button
                                    class="js-quantity-button px-3 py-2 text-gray-600 hover:text-yellow-600 {{ $displayStock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changeQuantity(-1)"
                                    aria-label="Diminuer la quantité" {{ $displayStock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span id="quantity" class="px-3 py-1">1</span>
                                <button
                                    class="js-quantity-button px-3 py-2 text-gray-600 hover:text-yellow-600 {{ $displayStock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changeQuantity(1)"
                                    aria-label="Augmenter la quantité" {{ $displayStock == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button id="addToCartButton"
                                class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-6 rounded-lg font-medium transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-yellow-500 {{ $displayStock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $displayPrice }}"
                                data-image="{{ $displayImage }}"
                                data-stock="{{ $displayStock }}" data-quantity="1"
                                data-variant-id="{{ $selectedVariant['id'] ?? '' }}"
                                data-variant-label="{{ $selectedVariant['label'] ?? '' }}"
                                onclick="addToCart(this)"
                                aria-label="Ajouter {{ $product->name }} au panier"
                                {{ $displayStock == 0 ? 'disabled' : '' }}>
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

        {{-- Description détaillée --}}
        @if ($product->hasDistinctLongDescription())
            <section class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden" aria-labelledby="description-produit">
                <div class="p-6">
                    <h2 id="description-produit" class="text-xl md:text-2xl font-bold text-gray-900 mb-4">
                        Description de {{ $product->name }}
                    </h2>
                    {{-- HTML déjà nettoyé par App\Support\RichText (liste blanche
                         de balises, aucun script ni attribut d'événement). --}}
                    <div class="product-description max-w-none text-gray-700">
                        {!! $product->safe_description !!}
                    </div>

                    @if ($primaryCategory)
                        <p class="mt-6 text-sm text-gray-500">
                            Retrouvez ce produit dans la catégorie
                            <a href="{{ $primaryCategory->url }}" class="text-yellow-600 hover:underline">{{ $primaryCategory->name }}</a>@if ($primaryCategory->parent)
                                , rayon <a href="{{ $primaryCategory->parent->url }}" class="text-yellow-600 hover:underline">{{ $primaryCategory->parent->name }}</a>@endif.
                        </p>
                    @endif
                </div>
            </section>
        @endif

        @include('front-office.partials.faq', [
            'faqs' => $productFaqs,
            'heading' => 'Questions fréquentes sur ' . $product->name,
            'uid' => 'faq-produit',
        ])

        <!-- Reviews Section -->
        <div class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Avis clients</h2>
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
                                <img src="https://i.pravatar.cc/128?img={{ rand(1, 70) }}" alt="{{ $review->name }}" loading="lazy"
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
                            class="relative bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
                            >
                            <div class="relative overflow-hidden flex-grow">
                                <a href="{{ route('preview-article', $item->slug) }}" target="_blank"
                                    title="{{ $item->meta_title ?? $item->name }}" class="block h-full">
                                    <img src="{{ $item->cover_url }}"
                                        alt="{{ $item->name }}"
                                        class="h-48 sm:h-56 w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out"
                                        loading="lazy" />
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
                                            >
                                            <a href="{{ route('preview-article', $item->slug) }}" class="after:absolute after:inset-0 after:content-['']">{{ $item->name }}</a>
                                        </h3>
                                        @if (($item->approved_reviews_count ?? 0) > 0)
                                            <p class="ml-2 flex shrink-0 items-center gap-1 text-xs text-gray-600">
                                                <span aria-hidden="true" class="text-yellow-400">★</span>
                                                {{ number_format($item->approved_reviews_avg, 1, ',', ' ') }}
                                            </p>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                        {{ Str::limit($item->short_description, 60) }}</p>
                                </div>
                                <div class="mt-auto">
                                    <div class="flex justify-between items-center">
                                        <p class="text-black font-bold text-lg" >
                                            @if ($item->has_variants)<span class="block text-xs font-normal text-gray-500">À partir de</span>@endif
                                            <span >{{ number_format($item->price, 2) }}</span> DT
                                            <meta  content="TND">
                                        </p>
                                        @if ($item->has_variants)
                                            <a href="{{ route('preview-article', $item->slug) }}" aria-label="Choisir une option pour {{ $item->name }}"
                                               class="relative z-10 flex items-center justify-center bg-black hover:bg-gray-800 text-white px-3 py-2 rounded-lg transition-colors duration-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @else
                                        <button aria-label="Ajouter {{ $item->name }} au panier"
                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-price="{{ $item->price }}"
                                            data-image="{{ $item->cover_url }}"
                                            data-stock="{{ $item->stock }}"
                                            class="relative z-10 cursor-pointer flex items-center justify-center gap-1 bg-black hover:bg-gray-800 text-white px-3 py-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transform hover:scale-[1.03] active:scale-95 text-sm sm:text-base"
                                            @if ($item->stock == 0) disabled @endif onclick="addToCart(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span class="hidden sm:inline">Ajouter</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <meta
                                content="{{ $item->meta_description ?? Str::limit($item->short_description, 80) }}">
                            <meta  content="{{ route('preview-article', $item->slug) }}">
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
        /* Mise en forme du contenu éditorial de la description longue. */
        .product-description { line-height: 1.75; overflow-wrap: anywhere; }
        .product-description > *:first-child { margin-top: 0; }
        .product-description p { margin: 0 0 1rem; }
        .product-description h2 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 1.75rem 0 .75rem; }
        .product-description h3 { font-size: 1.05rem; font-weight: 600; color: #374151; margin: 1.5rem 0 .5rem; }
        .product-description ul,
        .product-description ol { margin: 0 0 1rem; padding-left: 1.5rem; }
        .product-description ul { list-style: disc; }
        .product-description ol { list-style: decimal; }
        .product-description li { margin-bottom: .35rem; }
        .product-description a { color: #b8860b; text-decoration: underline; }
        .product-description a:hover { color: #92400e; }
        .product-description strong { font-weight: 600; color: #111827; }
        .product-description blockquote {
            margin: 1.25rem 0; padding: .5rem 0 .5rem 1rem;
            border-left: 3px solid #dfb54e; color: #4b5563; font-style: italic;
        }
        @media (max-width: 640px) {
            .product-description h2 { font-size: 1.15rem; }
            .product-description ul,
            .product-description ol { padding-left: 1.25rem; }
        }
    </style>
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
    <script>
        // ViewContent : consultation réelle d'une fiche, données du produit.
        window.paradisTrack && window.paradisTrack('ViewContent', {
            // Même identifiant que AddToCart et Purchase : Meta relie ainsi les évènements.
            content_ids: [@json((string) $product->id)],
            content_name: @json($product->name),
            content_type: 'product',
            value: {{ (float) $product->price }},
            currency: @json($config->currency ?: 'TND')
        });
    </script>

    @include('front-office.partials.faq-js')

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

            // Quantité et variantes. Le nom updateQuantity est réservé au panier
            // (layout) : le réutiliser ici cassait ses boutons + et −.
            const quantityDisplay = document.getElementById('quantity');
            const addToCartButton = document.getElementById('addToCartButton');

            const setQuantity = (quantity) => {
                quantityDisplay.textContent = quantity;
                addToCartButton.dataset.quantity = quantity;
            };

            window.changeQuantity = (change) => {
                const stock = Number(addToCartButton.dataset.stock);
                const quantity = Number(quantityDisplay.textContent) + change;

                if (quantity < 1) {
                    showNotification('La quantité minimale est 1.');
                } else if (quantity > stock) {
                    showNotification('Stock maximum atteint.', 'error');
                } else {
                    setQuantity(quantity);
                }
            };

            const variantPicker = document.getElementById('variantPicker');

            if (variantPicker) {
                const variants = @json($variants);
                const options = variantPicker.querySelectorAll('.variant-option');
                const axes = ['type', 'size'].filter(axis => variants.some(v => v[axis]));
                let selected = variants.find(v => v.id === {{ $selectedVariant['id'] ?? 'null' }});

                variants.forEach(v => { new Image().src = v.image; });

                // Tout clic aboutit sur une combinaison réelle : on garde l'autre
                // axe s'il existe avec cette valeur, sinon on prend la meilleure.
                // Le barré des options s'appuie sur ce même résultat.
                const resolveVariant = (axis, value) => {
                    const otherAxis = axes.find(a => a !== axis);
                    const candidates = variants.filter(v => v[axis] === value);

                    return candidates.find(v => otherAxis && v[otherAxis] === selected[otherAxis])
                        ?? candidates.find(v => v.stock > 0)
                        ?? candidates[0];
                };

                const selectVariant = (variant) => {
                    selected = variant;

                    options.forEach(option => {
                        const axis = option.dataset.axis;
                        const isSelected = variant[axis] === option.dataset.value;
                        const inStock = resolveVariant(axis, option.dataset.value).stock > 0;

                        option.setAttribute('aria-pressed', isSelected);
                        option.classList.toggle('border-yellow-600', isSelected);
                        option.classList.toggle('bg-yellow-50', isSelected);
                        option.classList.toggle('text-yellow-800', isSelected);
                        option.classList.toggle('border-gray-300', !isSelected);
                        option.classList.toggle('text-gray-700', !isSelected);
                        option.classList.toggle('line-through', !inStock);
                        option.classList.toggle('opacity-50', !inStock);
                    });

                    axes.forEach(axis => {
                        variantPicker.querySelector(`[data-axis-selected="${axis}"]`).textContent = variant[axis];
                    });

                    const available = variant.stock > 0;

                    document.getElementById('productPrice').textContent = variant.price
                        .toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('stockInStock').classList.toggle('hidden', !available);
                    document.getElementById('stockInStock').classList.toggle('flex', available);
                    document.getElementById('stockOut').classList.toggle('hidden', available);
                    document.getElementById('stockOut').classList.toggle('flex', !available);
                    mainImage.src = variant.image;

                    Object.assign(addToCartButton.dataset, {
                        price: variant.price,
                        image: variant.image,
                        stock: variant.stock,
                        variantId: variant.id,
                        variantLabel: variant.label,
                    });

                    [addToCartButton, ...document.querySelectorAll('.js-quantity-button')].forEach(button => {
                        button.disabled = !available;
                        button.classList.toggle('opacity-50', !available);
                        button.classList.toggle('cursor-not-allowed', !available);
                    });

                    setQuantity(1);
                };

                options.forEach(option => option.addEventListener('click', () => {
                    selectVariant(resolveVariant(option.dataset.axis, option.dataset.value));
                }));

                // ?variant=ID : lien direct vers une option (données structurées).
                const requested = Number(new URLSearchParams(location.search).get('variant'));
                selectVariant(variants.find(v => v.id === requested) ?? selected);
            }

        // addToCart est défini une seule fois dans front-office/layouts/app.blade.php
        // (notification, ouverture du panier, compteur, évènement de tracking).
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
        });
    </script>
@endsection
