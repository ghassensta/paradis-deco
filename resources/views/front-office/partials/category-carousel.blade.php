{{--
    Carousel de grandes catégories — compact, réutilise le Swiper déjà chargé
    par la page d'accueil et la configuration partagée `data-swiper`.

    $categories : collection de catégories (avec `url` et `visual_url`)
    $uid        : identifiant unique du carousel sur la page
    $eyebrow / $title / $subtitle / $ctaUrl / $ctaLabel

    La liste reste un <ul> de vrais liens : sans JavaScript, les catégories
    défilent horizontalement et restent toutes cliquables et indexables.
--}}
@if ($categories->isNotEmpty())
    @php
        $uid = $uid ?? 'categories';
    @endphp

    <section class="py-10 md:py-14" aria-labelledby="{{ $uid }}-title">
        <div class="container mx-auto px-5 sm:px-6">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4 md:mb-8">
                <div>
                    @isset($eyebrow)
                        <p class="mb-1.5 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">{{ $eyebrow }}</p>
                    @endisset
                    <h2 id="{{ $uid }}-title" class="text-2xl font-semibold tracking-tight text-gray-900 md:text-3xl">
                        {{ $title }}
                    </h2>
                    @isset($subtitle)
                        <p class="mt-2 max-w-xl text-sm text-gray-500 md:text-base">{{ $subtitle }}</p>
                    @endisset
                </div>

                {{-- Masquées tant que tout tient à l'écran : Swiper retire
                     `swiper-button-lock` dès qu'un défilement est possible. --}}
                <div class="flex items-center gap-2">
                    <button type="button"
                            class="js-prev-{{ $uid }} flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                            aria-label="Catégories précédentes">
                        <span aria-hidden="true">&#8249;</span>
                    </button>
                    <button type="button"
                            class="js-next-{{ $uid }} flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                            aria-label="Catégories suivantes">
                        <span aria-hidden="true">&#8250;</span>
                    </button>
                </div>
            </div>

            <div class="swiper js-carousel-{{ $uid }}"
                 data-swiper='{"slidesPerView":2.2,"spaceBetween":12,"breakpoints":{"480":{"slidesPerView":3.2,"spaceBetween":14},"768":{"slidesPerView":4,"spaceBetween":18},"1024":{"slidesPerView":5,"spaceBetween":22}}}'
                 data-prev-label="Catégories précédentes"
                 data-next-label="Catégories suivantes">
                <ul class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <li class="swiper-slide h-auto">
                            <a href="{{ $category->url }}" class="group block focus:outline-none">
                                {{-- Vignette carrée : même gabarit pour toutes les cartes,
                                     et une hauteur de section très inférieure à la grille 4/5. --}}
                                <div class="relative aspect-square overflow-hidden rounded-xl bg-[#faf8f4]">
                                    {{-- Vignette dédiée : les visuels historiques (jusqu'à
                                         3024 px) ne sont pas servis en pleine résolution
                                         dans une carte de ~230 px. --}}
                                    <img src="{{ \App\Support\ImageStorage::variantUrl($category->visual_url, 480) }}"
                                         alt="{{ $category->name }}"
                                         width="480" height="480" loading="lazy" decoding="async"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.05]"
                                         sizes="(min-width: 1024px) 19vw, (min-width: 768px) 23vw, (min-width: 480px) 30vw, 44vw">
                                    <span class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-black/5"></span>
                                </div>

                                <div class="mt-3">
                                    <span class="block text-[13px] font-medium leading-snug text-gray-900 transition-colors group-hover:text-[#dfb54e] md:text-sm">
                                        {{ $category->name }}
                                    </span>
                                    <span class="mt-1.5 block h-px w-6 bg-[#dfb54e] transition-all duration-300 group-hover:w-12"></span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @isset($ctaUrl)
                <div class="mt-8 text-center">
                    <a href="{{ $ctaUrl }}"
                       class="inline-flex items-center gap-2 border-b border-gray-300 pb-1 text-sm font-medium text-gray-700 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e]">
                        {{ $ctaLabel ?? 'Voir toutes les collections' }}
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            @endisset
        </div>
    </section>
@endif
