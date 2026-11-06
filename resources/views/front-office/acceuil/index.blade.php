@extends('front-office.layouts.app')

@section('description', 'Paradis Déco, boutique déco n°1 en Tunisie : meubles, luminaires, tapis et accessoires artisanaux pour un intérieur moderne et chaleureux. Livraison rapide partout en Tunisie.')
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

    <meta name="keywords" content="{{ implode(', ', $keywords) }}">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

    <link rel="canonical" href="{{ config('app.url', 'https://example.com') }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    <!-- Robots -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph / Facebook -->
    <meta property="og:image:alt" content="Salon décoré avec meubles et luminaires de Paradis Déco">

    <!-- Twitter Card -->
    <!-- JSON-LD Schema.org (tout le balisage structuré ici) -->
    @php
        // Graphe construit en PHP : json_encode garantit un JSON valide quels que
        // soient les apostrophes, guillemets ou accents présents dans les données.
        $home = url('/');
        $siteName = $config->site_name ?: config('app.name');

        $graph = [
            [
                '@type' => 'Organization',
                '@id' => $home . '/#organization',
                'name' => $siteName,
                'url' => $home,
                'logo' => $ogImage,
                'sameAs' => [
                    'https://www.facebook.com/paradisdeco.tn',
                    'https://www.instagram.com/paradisdeco.tn',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $home . '/#website',
                'url' => $home,
                'name' => $siteName,
                'publisher' => ['@id' => $home . '/#organization'],
            ],
            [
                '@type' => 'WebPage',
                '@id' => url()->current() . '/#webpage',
                'url' => url()->current(),
                'name' => $siteName . ' – Boutique déco en ligne Tunisie',
                'isPartOf' => ['@id' => $home . '/#website'],
                'breadcrumb' => ['@id' => url()->current() . '/#breadcrumb'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => url()->current() . '/#breadcrumb',
                'itemListElement' => [[
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Accueil',
                    'item' => $home,
                ]],
            ],
        ];

        $items = [];

        foreach ($latestProducts->where('is_active', true)->values() as $index => $item) {
            $product = [
                '@type' => 'Product',
                '@id' => route('preview-article', $item->slug) . '#product',
                'name' => $item->name,
                'description' => Str::limit(strip_tags($item->description), 180),
                // Référence réelle du catalogue, jamais une valeur fabriquée.
                'sku' => $item->sku ?: (string) $item->id,
                'brand' => ['@type' => 'Brand', 'name' => $siteName],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => route('preview-article', $item->slug),
                    'priceCurrency' => 'TND',
                    'price' => number_format((float) $item->price, 2, '.', ''),
                    'availability' => $item->stock > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'seller' => ['@type' => 'Organization', 'name' => $siteName],
                ],
            ];

            if ($cover = \App\Models\Product::mediaUrl($item->image_avant)) {
                $product['image'] = $cover;
            }

            // Note agrégée déclarée uniquement si des avis approuvés existent.
            if (($item->approved_reviews_count ?? 0) > 0 && $item->approved_reviews_avg !== null) {
                $product['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => number_format((float) $item->approved_reviews_avg, 1, '.', ''),
                    'reviewCount' => (int) $item->approved_reviews_count,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ];
            }

            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $product,
            ];
        }

        if ($items) {
            $graph[] = [
                '@type' => 'ItemList',
                'name' => 'Nouveautés – Produits en vedette',
                'itemListElement' => $items,
            ];
        }

        if ($testimonials->isNotEmpty()) {
            $graph[] = [
                '@type' => 'Organization',
                '@id' => $home . '/#organization',
                'review' => $testimonials->map(fn ($t) => [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (string) $t->rating,
                        'bestRating' => '5',
                    ],
                    'author' => ['@type' => 'Person', 'name' => $t->name],
                    'reviewBody' => Str::limit($t->comment, 300),
                    'datePublished' => $t->created_at->format('Y-m-d'),
                ])->all(),
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection
@section('og_image', $ogImage)

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
                        class="w-full h-full object-cover" loading="eager" fetchpriority="high" decoding="async" />

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

    {{-- ================= UNIVERS / CATÉGORIES =================
         Carousel compact : seules les catégories racines sont exposées ici,
         les sous-catégories restent accessibles depuis la page catégorie. --}}
    @include('front-office.partials.category-carousel', [
        'categories' => $rootCategories,
        'eyebrow' => 'Nos collections',
        'title' => 'Explorez nos univers',
        'subtitle' => 'Une sélection pensée pour chaque pièce de votre intérieur.',
        'uid' => 'univers',
    ])

    {{-- ================= DERNIÈRES NOUVEAUTÉS ================= --}}
    <div class="bg-[#faf8f4]">
        @include('front-office.partials.product-carousel', [
            'products' => $latestProducts,
            'title' => 'Nos dernières nouveautés',
            'subtitle' => 'Les dernières pièces ajoutées à la boutique.',
            'ctaUrl' => route('allproduits'),
            'ctaLabel' => 'Voir tous les produits',
            'uid' => 'nouveautes',
        ])
    </div>

    {{-- ============ MEILLEURES VENTES (données réelles) ============
         Masqué tant qu'aucune vente n'a été enregistrée : aucun
         classement n'est inventé. --}}
    @include('front-office.partials.product-carousel', [
        'products' => $bestSellers,
        'title' => 'Nos incontournables',
        'subtitle' => 'Les pièces les plus commandées par nos clients.',
        'ctaUrl' => route('allproduits'),
        'ctaLabel' => 'Découvrir la boutique',
        'uid' => 'incontournables',
    ])

    {{-- ================= SECTION ÉDITORIALE ================= --}}
    @if ($rootCategories->isNotEmpty())
        @php $editorialCategory = $rootCategories->first(); @endphp
        <section class="py-14 md:py-20" aria-labelledby="editorial-title">
            <div class="container mx-auto px-5 sm:px-6">
                <div class="grid items-center gap-8 md:grid-cols-2 md:gap-14">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-[#faf8f4] md:aspect-[5/4]">
                        <img src="{{ $editorialCategory->visual_url }}"
                             alt="Intérieur décoré par {{ $config->site_name }}"
                             width="800" height="640" loading="lazy" decoding="async"
                             class="h-full w-full object-cover"
                             sizes="(min-width: 768px) 50vw, 100vw">
                    </div>

                    <div>
                        <p class="mb-2 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">Notre approche</p>
                        <h2 id="editorial-title" class="text-2xl font-semibold tracking-tight text-gray-900 md:text-3xl">
                            L'élégance dans chaque détail
                        </h2>
                        <p class="mt-4 text-sm leading-relaxed text-gray-600 md:text-base">
                            Chaque pièce est choisie pour sa matière, sa finition et la façon dont elle
                            s'intègre à un intérieur. Des accessoires du quotidien aux pièces plus
                            affirmées, notre sélection privilégie la sobriété et la durabilité.
                        </p>

                        {{-- Arguments tirés de la configuration réelle du site. --}}
                        <ul class="mt-6 space-y-2.5 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <span aria-hidden="true" class="mt-0.5 text-[#dfb54e]">&#10003;</span>
                                <span>{{ $rootCategories->count() }} univers à explorer</span>
                            </li>
                            @if ($config->city)
                                <li class="flex items-start gap-2">
                                    <span aria-hidden="true" class="mt-0.5 text-[#dfb54e]">&#10003;</span>
                                    <span>Expédition depuis {{ $config->city }}</span>
                                </li>
                            @endif
                            @if (($config->delivery_estimate_days ?? 0) > 0)
                                <li class="flex items-start gap-2">
                                    <span aria-hidden="true" class="mt-0.5 text-[#dfb54e]">&#10003;</span>
                                    <span>Livraison estimée sous {{ $config->delivery_estimate_days }} jour(s)</span>
                                </li>
                            @endif
                            @if (($config->free_shipping_threshold ?? 0) > 0)
                                <li class="flex items-start gap-2">
                                    <span aria-hidden="true" class="mt-0.5 text-[#dfb54e]">&#10003;</span>
                                    <span>Livraison offerte dès {{ number_format($config->free_shipping_threshold, 0, ',', ' ') }} DT d'achat</span>
                                </li>
                            @endif
                        </ul>

                        <a href="{{ route('allproduits') }}"
                           class="mt-7 inline-block rounded-lg bg-gray-900 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e] focus:ring-offset-2">
                            Découvrir la boutique
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ UN INTÉRIEUR À VOTRE IMAGE ============ --}}
    @php $featuredUniverses = $rootCategories->slice(0, 4)->values(); @endphp
    @if ($featuredUniverses->count() >= 2)
        <section class="bg-[#faf8f4] py-10 md:py-14" aria-labelledby="interieur-title">
            <div class="container mx-auto px-5 sm:px-6">
                <div class="mb-6 md:mb-8">
                    <h2 id="interieur-title" class="text-2xl font-semibold tracking-tight text-gray-900 md:text-3xl">
                        Un intérieur à votre image
                    </h2>
                    <p class="mt-2 max-w-xl text-sm text-gray-500 md:text-base">
                        Parcourez nos collections et composez l'ambiance qui vous ressemble.
                    </p>
                </div>

                {{-- Cartes horizontales : la vignette reste petite et le texte
                     passe sur fond blanc, donc toujours lisible quelle que soit
                     la photo (une image claire rendait le texte en surimpression
                     illisible). La vignette 480 px est celle déjà générée pour
                     le carousel : aucun fichier supplémentaire. --}}
                <ul class="grid gap-3 sm:gap-4 md:grid-cols-2">
                    @foreach ($featuredUniverses as $universe)
                        <li>
                            <a href="{{ $universe->url }}"
                               class="group flex h-full items-center gap-4 rounded-xl bg-white p-3 ring-1 ring-black/5 transition-shadow hover:shadow-[0_2px_16px_rgba(0,0,0,0.06)] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]">
                                <span class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-[#faf8f4] sm:h-24 sm:w-24">
                                    <img src="{{ \App\Support\ImageStorage::variantUrl($universe->visual_url, 480) }}"
                                         alt="{{ $universe->name }}"
                                         width="480" height="480" loading="lazy" decoding="async"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.05]"
                                         sizes="96px">
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="block text-[15px] font-medium leading-snug text-gray-900 transition-colors group-hover:text-[#dfb54e]">
                                        {{ $universe->name }}
                                    </span>
                                    @if ($universe->short_description)
                                        <span class="mt-1 line-clamp-2 text-xs leading-relaxed text-gray-500 sm:text-[13px]">
                                            {{ \Illuminate\Support\Str::limit($universe->short_description, 90) }}
                                        </span>
                                    @endif
                                    <span class="mt-2 inline-block border-b border-gray-300 pb-0.5 text-[11px] uppercase tracking-[0.12em] text-gray-500 transition-colors group-hover:border-[#dfb54e] group-hover:text-[#dfb54e]">
                                        Découvrir
                                    </span>
                                </span>

                                <span aria-hidden="true"
                                      class="shrink-0 text-gray-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#dfb54e]">&rarr;</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                </div>
            </div>
        </section>
    @endif

    {{-- ================= RÉASSURANCE =================
         Uniquement des informations issues de la configuration réelle :
         aucune promesse de retour, de garantie ou de gratuité inventée. --}}
    <section class="py-14 md:py-20" aria-labelledby="reassurance-title">
        <div class="container mx-auto px-5 sm:px-6">
            <h2 id="reassurance-title" class="mb-8 text-center text-2xl font-semibold tracking-tight text-gray-900 md:mb-12 md:text-3xl">
                Pourquoi choisir {{ $config->site_name }}
            </h2>

            @php
                $assurances = collect([
                    [
                        'icon' => 'fa-gem',
                        'title' => 'Une sélection soignée',
                        'text' => 'Chaque pièce est choisie pour sa matière et sa finition.',
                    ],
                    $config->city ? [
                        'icon' => 'fa-truck',
                        'title' => 'Expédition depuis ' . $config->city,
                        'text' => ($config->delivery_estimate_days ?? 0) > 0
                            ? 'Livraison estimée sous ' . $config->delivery_estimate_days . ' jour(s).'
                            : 'Vos commandes préparées avec soin.',
                    ] : null,
                    ($config->free_shipping_threshold ?? 0) > 0 ? [
                        'icon' => 'fa-box-open',
                        'title' => 'Livraison offerte',
                        'text' => "Dès " . number_format($config->free_shipping_threshold, 0, ',', ' ') . " DT d'achat.",
                    ] : null,
                    ($config->support_phone || $config->support_email) ? [
                        'icon' => 'fa-headset',
                        'title' => 'Une équipe joignable',
                        'text' => $config->support_phone ?: $config->support_email,
                    ] : null,
                ])->filter()->values();
            @endphp

            <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-{{ min(4, max(1, $assurances->count())) }}">
                @foreach ($assurances as $item)
                    <li class="rounded-2xl border border-gray-100 p-6 text-center">
                        <span class="mx-auto mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-[#faf8f4]">
                            <i class="fa-solid {{ $item['icon'] }} text-[#dfb54e]" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-[15px] font-medium text-gray-900">{{ $item['title'] }}</h3>
                        <p class="mt-1.5 text-sm text-gray-500">{{ $item['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

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
                            <img src="{{ $inspiration->image ? asset('storage/' . $inspiration->image) : asset('images/no-image.png') }}" alt="{{ $inspiration->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                loading="lazy" width="500" height="320" decoding="async"
                                sizes="(min-width: 1024px) 25vw,
                                       (min-width: 768px) 33vw,
                                       (min-width: 640px) 50vw,
                                       100vw">
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

    <!-- TÉMOIGNAGES — masqués tant qu'aucun avis n'est approuvé -->
    @if ($testimonials->isNotEmpty())
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
                            {{-- Aucune photo client n'est collectée : l'avatar est composé
                                 des initiales, sans dépendance à un service externe. --}}
                            <span aria-hidden="true"
                                class="mr-2 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold text-gray-700">
                                {{ Str::of($testimonial->name)->explode(' ')->filter()->take(2)->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->implode('') }}
                            </span>
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
    @endif

    {{-- ================= CONTENU SEO ================= --}}
    <section class="border-t border-gray-100 bg-[#faf8f4] py-12 md:py-16" aria-labelledby="seo-title">
        <div class="container mx-auto max-w-3xl px-5 sm:px-6">
            <h2 id="seo-title" class="text-xl font-semibold tracking-tight text-gray-900 md:text-2xl">
                {{ $config->site_name }}, votre univers décoration en Tunisie
            </h2>

            <div class="mt-4 space-y-3 text-sm leading-relaxed text-gray-600">
                <p>
                    {{ $config->site_name }} réunit une sélection d'objets de décoration pour la maison :
                    @if ($rootCategories->isNotEmpty())
                        {{ $rootCategories->pluck('name')->implode(', ') }}.
                    @else
                        accessoires, textiles et pièces décoratives.
                    @endif
                    Chaque référence est présentée avec ses caractéristiques et ses dimensions
                    pour vous aider à choisir sereinement.
                </p>
                @php
                    // Phrase assemblée en PHP : enchaîner des directives Blade
                    // sur une même ligne empêche la compilation de la seconde.
                    $deliveryParts = array_filter([
                        $config->city ? 'depuis toute la Tunisie, au départ de ' . $config->city : null,
                        ($config->delivery_estimate_days ?? 0) > 0
                            ? 'avec une livraison estimée sous ' . $config->delivery_estimate_days . ' jour(s)'
                            : null,
                    ]);
                @endphp
                <p>
                    Vous pouvez commander en ligne{{ $deliveryParts ? ' ' . implode(', ', $deliveryParts) : '' }}.
                    Une question sur un article ou sur votre commande ?
                    <a href="{{ route('contact') }}" class="text-[#a8842f] underline underline-offset-2 hover:text-[#dfb54e]">Contactez-nous</a>,
                    ou consultez notre <a href="{{ route('faq') }}" class="text-[#a8842f] underline underline-offset-2 hover:text-[#dfb54e]">foire aux questions</a>.
                </p>
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

        /* Carousel catégories : tant que Swiper n'a pas pris la main (script
           différé, ou JavaScript indisponible), les cartes restent parcourables
           au doigt plutôt que d'être coupées. */
        .js-carousel-univers:not(.swiper-initialized) {
            overflow-x: auto;
            scrollbar-width: none;
        }

        .js-carousel-univers:not(.swiper-initialized)::-webkit-scrollbar {
            display: none;
        }

        .js-carousel-univers:not(.swiper-initialized) .swiper-slide {
            width: 44%;
            margin-right: 12px;
        }

        @media (min-width: 768px) {
            .js-carousel-univers:not(.swiper-initialized) .swiper-slide {
                width: 23%;
                margin-right: 18px;
            }
        }

        @media (min-width: 1024px) {
            .js-carousel-univers:not(.swiper-initialized) .swiper-slide {
                width: 19%;
                margin-right: 22px;
            }
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
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swiper === 'undefined') {
                return;
            }

            // Pas d'autoplay : le défilement reste à la main du visiteur.
            document.querySelectorAll('[class*="js-carousel-"]').forEach((node) => {
                const uid = (node.className.match(/js-carousel-([\w-]+)/) || [])[1];

                if (!uid) {
                    return;
                }

                // Réglages produits par défaut : 2 cartes lisibles dès 375 px,
                // 4 en desktop. Un carousel peut les surcharger via data-swiper
                // (les catégories, plus compactes, en affichent davantage).
                let surcharge = {};

                try {
                    surcharge = JSON.parse(node.dataset.swiper || '{}');
                } catch (e) {
                    surcharge = {};
                }

                new Swiper(node, {
                    slidesPerView: 2,
                    spaceBetween: 12,
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 16 },
                        768: { slidesPerView: 3, spaceBetween: 20 },
                        1024: { slidesPerView: 4, spaceBetween: 24 },
                    },
                    ...surcharge,
                    grabCursor: true,
                    watchOverflow: true,
                    a11y: {
                        prevSlideMessage: node.dataset.prevLabel || 'Produits précédents',
                        nextSlideMessage: node.dataset.nextLabel || 'Produits suivants',
                    },
                    navigation: {
                        prevEl: `.js-prev-${uid}`,
                        nextEl: `.js-next-${uid}`,
                    },
                });
            });
        });
    </script>
@endsection
