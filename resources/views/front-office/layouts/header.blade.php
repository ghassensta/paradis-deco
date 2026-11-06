@php
    $navCategories = $navCategories ?? collect();
    $logoUrl = $config->site_logo
        ? asset('storage/' . $config->site_logo)
        : asset('assets/img/cover-image-removebg-preview.png');
    $moitie = (int) ceil($navCategories->count() / 2);
    $categoriesGauche = $navCategories->take($moitie);
    $categoriesDroite = $navCategories->slice($moitie);
@endphp

<header class="sticky top-0 z-40 bg-white shadow-sm">
    {{-- Rangée principale (mobile et tablette) : menu, logo, panier. --}}
    <div class="container mx-auto px-5 sm:px-6">
        <div class="grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-3 lg:hidden">

            <div class="flex items-center">
                <button id="mobileMenuButton" type="button"
                        class="-ml-1 flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 transition-colors hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e] lg:hidden"
                        aria-label="Ouvrir le menu" aria-controls="mobileMenuOverlay" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

            </div>

            <a href="{{ route('welcome') }}" class="justify-self-center" aria-label="{{ $config->site_name ?? 'Paradis Déco' }} — accueil">
                <img src="{{ $logoUrl }}" width="80" height="100"
                     alt="Logo-{{ $config->site_name }}"
                     class="h-12 w-auto">
            </a>

            <div class="flex items-center justify-end gap-4">
                <button type="button" aria-label="Ouvrir le panier"
                        class="cart-button relative flex items-center gap-1.5 text-gray-600 transition-colors hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cartCount" data-cart-count class="text-sm font-semibold">0</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Rangée collections (desktop) : le logo est posé au centre, les grandes
         catégories se répartissant de part et d'autre. Chaque catégorie reste
         un vrai lien ; ses sous-catégories s'ouvrent au survol comme au clavier
         (focus-within), sans JavaScript. --}}
    @if ($navCategories->isNotEmpty())
        <nav class="hidden lg:block" aria-label="Collections">
            <div class="container relative mx-auto px-5 sm:px-6">
                {{-- Le panier est hors de la grille : compté dans une colonne, il
                     élargissait le côté droit et décalait le logo du centre. --}}
                <div class="grid h-20 grid-cols-[1fr_auto_1fr] items-center gap-2">

                    <ul class="flex items-center justify-end">
                        @foreach ($categoriesGauche as $categorie)
                            @include('front-office.partials.nav-categorie', ['categorie' => $categorie, 'alignement' => $loop->first ? 'left-0' : 'left-1/2 -translate-x-1/2'])
                        @endforeach
                    </ul>

                    <a href="{{ route('welcome') }}" class="justify-self-center px-3"
                       aria-label="{{ $config->site_name ?? 'Paradis Déco' }} — accueil">
                        <img src="{{ $logoUrl }}" width="80" height="100"
                             alt="Logo-{{ $config->site_name }}" class="h-14 w-auto xl:h-16">
                    </a>

                    <ul class="flex items-center justify-start pr-10 xl:pr-12">
                        @foreach ($categoriesDroite as $categorie)
                            @include('front-office.partials.nav-categorie', ['categorie' => $categorie, 'alignement' => $loop->last ? 'right-0' : 'left-1/2 -translate-x-1/2'])
                        @endforeach
                    </ul>
                </div>

                <button type="button" aria-label="Ouvrir le panier"
                        class="cart-button absolute right-5 top-1/2 flex -translate-y-1/2 items-center gap-1.5 text-gray-600 transition-colors hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e] sm:right-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span data-cart-count class="text-sm font-semibold">0</span>
                </button>
            </div>
        </nav>
    @endif

    {{-- Menu mobile --}}
    <div id="mobileMenuOverlay" class="fixed inset-0 z-50 hidden bg-black/30 backdrop-blur-sm transition-opacity duration-300">
        <div class="absolute right-0 top-0 flex h-full w-[85%] max-w-sm translate-x-full flex-col bg-white shadow-xl transition-transform duration-300 ease-in-out">
            <div class="flex items-center justify-between border-b border-gray-100 p-5">
                <img src="{{ $logoUrl }}" width="80" height="100" alt="Logo-{{ $config->site_name }}" class="h-10 w-auto">
                <button id="closeMobileMenu" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 transition-colors hover:text-[#dfb54e] focus:outline-none focus:ring-2 focus:ring-[#dfb54e]"
                        aria-label="Fermer le menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-5" aria-label="Navigation mobile">
                @if ($navCategories->isNotEmpty())
                    <p class="mb-2 px-3 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">Nos collections</p>
                    <ul class="space-y-1">
                        @foreach ($navCategories as $categorie)
                            @php $sousCategories = $categorie->activeChildren; @endphp
                            <li>
                                @if ($sousCategories->isNotEmpty())
                                    {{-- <details> : accordéon natif, opérationnel sans JavaScript. --}}
                                    <details class="group rounded-lg">
                                        <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2.5 text-[15px] text-gray-700 transition-colors hover:bg-[#faf8f4] hover:text-[#dfb54e]">
                                            {{ $categorie->name }}
                                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200 group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.19l3.71-3.96a.75.75 0 111.08 1.04l-4.25 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </summary>
                                        <ul class="mb-1 ml-3 space-y-0.5 border-l border-gray-100 pl-3">
                                            <li>
                                                <a href="{{ $categorie->url }}" class="block rounded-lg px-3 py-2 text-[13px] text-gray-500 transition-colors hover:text-[#dfb54e]">
                                                    Toute la collection
                                                </a>
                                            </li>
                                            @foreach ($sousCategories as $sousCategorie)
                                                <li>
                                                    <a href="{{ $sousCategorie->url }}" class="block rounded-lg px-3 py-2 text-[14px] text-gray-600 transition-colors hover:text-[#dfb54e]">
                                                        {{ $sousCategorie->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @else
                                    <a href="{{ $categorie->url }}" class="block rounded-lg px-3 py-2.5 text-[15px] text-gray-700 transition-colors hover:bg-[#faf8f4] hover:text-[#dfb54e]">
                                        {{ $categorie->name }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </nav>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bouton = document.getElementById('mobileMenuButton');
    const fermer = document.getElementById('closeMobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');

    if (!bouton || !overlay) {
        return;
    }

    const panneau = overlay.firstElementChild;

    function ouvrir() {
        overlay.classList.remove('hidden');
        bouton.setAttribute('aria-expanded', 'true');
        requestAnimationFrame(() => panneau.classList.remove('translate-x-full'));
    }

    function fermerMenu() {
        panneau.classList.add('translate-x-full');
        bouton.setAttribute('aria-expanded', 'false');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    bouton.addEventListener('click', ouvrir);
    fermer?.addEventListener('click', fermerMenu);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) fermerMenu(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) fermerMenu();
    });
});
</script>
