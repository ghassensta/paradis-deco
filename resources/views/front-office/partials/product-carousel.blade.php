{{--
    Carousel produits réutilisable (Swiper, déjà chargé par la page d'accueil).
    $products : collection de produits — la section n'est pas rendue si vide
    $title / $subtitle / $ctaUrl / $ctaLabel / $uid / $eager
--}}
@if ($products->isNotEmpty())
    @php
        $uid = $uid ?? 'carousel';
        $eager = $eager ?? false;
    @endphp

    <section class="py-14 md:py-20" aria-labelledby="{{ $uid }}-title">
        <div class="container mx-auto px-5 sm:px-6">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4 md:mb-10">
                <div>
                    <h2 id="{{ $uid }}-title" class="text-2xl font-semibold tracking-tight text-gray-900 md:text-3xl">
                        {{ $title }}
                    </h2>
                    @isset($subtitle)
                        <p class="mt-2 max-w-xl text-sm text-gray-500 md:text-base">{{ $subtitle }}</p>
                    @endisset
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" class="js-prev-{{ $uid }} flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                            aria-label="Produits précédents">
                        <span aria-hidden="true">&#8249;</span>
                    </button>
                    <button type="button" class="js-next-{{ $uid }} flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                            aria-label="Produits suivants">
                        <span aria-hidden="true">&#8250;</span>
                    </button>
                </div>
            </div>

            <div class="swiper js-carousel-{{ $uid }}">
                <div class="swiper-wrapper">
                    @foreach ($products as $product)
                        <div class="swiper-slide h-auto">
                            @include('front-office.partials.product-card', [
                                'product' => $product,
                                'eager' => $eager && $loop->first,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

            @isset($ctaUrl)
                <div class="mt-8 text-center">
                    <a href="{{ $ctaUrl }}"
                       class="inline-flex items-center gap-2 border-b border-gray-300 pb-1 text-sm font-medium text-gray-700 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e]">
                        {{ $ctaLabel ?? 'Voir tous les produits' }}
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            @endisset
        </div>
    </section>
@endif
