{{--
    Carte produit — source unique pour toutes les listes du front.
    $product : App\Models\Product
    $eager   : true pour les cartes au-dessus de la ligne de flottaison
--}}
@php
    $eager = $eager ?? false;
    // « Nouveau » uniquement si le produit l'est réellement.
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(30));
    $rating = (float) ($product->approved_reviews_avg ?? 0);
    $reviewCount = (int) ($product->approved_reviews_count ?? 0);
@endphp

<article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition-shadow duration-300 hover:shadow-lg">
    <a href="{{ route('preview-article', $product->slug) }}"
       class="relative block aspect-square overflow-hidden bg-[#faf8f4]"
       title="{{ $product->name }}">
        <img src="{{ $product->cover_url }}"
             alt="{{ $product->name }}"
             width="500" height="500" decoding="async"
             loading="{{ $eager ? 'eager' : 'lazy' }}"
             @if ($eager) fetchpriority="high" @endif
             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
             sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw">

        {{-- Badges : uniquement des états réels du produit. --}}
        <div class="absolute left-3 top-3 flex flex-col gap-1">
            @if ($product->stock == 0)
                <span class="rounded-full bg-gray-900/85 px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide text-white">Épuisé</span>
            @elseif ($product->stock <= 5)
                <span class="rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide text-gray-900">Dernières pièces</span>
            @endif

            @if ($isNew)
                <span class="rounded-full bg-[#dfb54e] px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide text-white">Nouveau</span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col gap-2 p-4">
        <h3 class="line-clamp-2 text-[15px] font-medium leading-snug text-gray-900">
            {{-- Lien étiré : toute la carte mène à la fiche ; les boutons restent au-dessus (z-10). --}}
            <a href="{{ route('preview-article', $product->slug) }}" class="transition-colors hover:text-[#dfb54e] after:absolute after:inset-0 after:content-['']">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Note affichée seulement si des avis approuvés existent. --}}
        @if ($reviewCount > 0)
            <p class="flex items-center gap-1 text-xs text-gray-500">
                <span aria-hidden="true" class="text-[#dfb54e]">★</span>
                <span>{{ number_format($rating, 1, ',', ' ') }}</span>
                <span class="sr-only">sur 5 —</span>
                <span>({{ $reviewCount }} avis)</span>
            </p>
        @endif

        <p class="mt-auto text-lg font-semibold text-gray-900">
            @if ($product->has_variants)
                <span class="block text-xs font-normal text-gray-500">À partir de</span>
            @endif
            {{ number_format($product->price, 2, ',', ' ') }} <span class="text-sm font-normal text-gray-500">DT</span>
        </p>

        @if ($product->has_variants)
            {{-- Le choix du type et de la taille se fait sur la fiche produit. --}}
            <a href="{{ route('preview-article', $product->slug) }}"
               class="relative z-10 mt-3 block w-full rounded-lg border border-[#dfb54e] px-4 py-2.5 text-center text-sm font-medium text-[#a8842f] transition-colors duration-200 hover:bg-[#dfb54e] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#dfb54e] focus:ring-offset-2">
                Choisir une option
            </a>
        @elseif ($product->stock > 0)
            {{-- Fonction panier globale : toast, compteur, drawer et tracking. --}}
            <button type="button"
                    onclick="addToCart(this)"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-image="{{ $product->cover_url }}"
                    data-stock="{{ $product->stock }}"
                    aria-label="Ajouter {{ $product->name }} au panier"
                    class="relative z-10 mt-3 w-full rounded-lg border border-[#dfb54e] px-4 py-2.5 text-sm font-medium text-[#a8842f] transition-colors duration-200 hover:bg-[#dfb54e] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#dfb54e] focus:ring-offset-2">
                Ajouter au panier
            </button>
        @else
            <span class="mt-3 block w-full rounded-lg border border-gray-200 px-4 py-2.5 text-center text-sm font-medium text-gray-400">
                Indisponible
            </span>
        @endif
    </div>
</article>
