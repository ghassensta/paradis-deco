{{--
    Une grande catégorie de la barre de navigation, avec son menu déroulant.
    $categorie  : catégorie racine (activeChildren déjà chargée)
    $alignement : classes de positionnement du menu (recentré, ou calé à droite
                  pour la dernière catégorie afin de ne pas sortir de l'écran)
--}}
@php $sousCategories = $categorie->activeChildren; @endphp

<li class="group relative">
    <a href="{{ $categorie->url }}"
       class="flex items-center gap-1 whitespace-nowrap px-1.5 py-3 text-[12px] font-medium text-gray-600 transition-colors hover:text-[#dfb54e] focus:text-[#dfb54e] focus:outline-none xl:px-3 xl:text-[13px]">
        {{ $categorie->name }}
        @if ($sousCategories->isNotEmpty())
            <svg class="h-3 w-3 shrink-0 text-gray-400 transition-transform duration-200 group-hover:rotate-180 group-focus-within:rotate-180"
                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.19l3.71-3.96a.75.75 0 111.08 1.04l-4.25 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        @endif
    </a>

    @if ($sousCategories->isNotEmpty())
        <div class="invisible absolute {{ $alignement }} top-full z-50 w-60 pt-1 opacity-0 transition-opacity duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
            <ul class="rounded-xl border border-gray-100 bg-white p-1.5 shadow-[0_8px_28px_rgba(0,0,0,0.10)]">
                @foreach ($sousCategories as $sousCategorie)
                    <li>
                        <a href="{{ $sousCategorie->url }}"
                           class="block rounded-lg px-3 py-2 text-[13px] text-gray-600 transition-colors hover:bg-[#faf8f4] hover:text-[#dfb54e] focus:bg-[#faf8f4] focus:text-[#dfb54e] focus:outline-none">
                            {{ $sousCategorie->name }}
                        </a>
                    </li>
                @endforeach
                <li class="mt-1 border-t border-gray-100 pt-1">
                    <a href="{{ $categorie->url }}"
                       class="block rounded-lg px-3 py-2 text-[11px] uppercase tracking-[0.12em] text-gray-400 transition-colors hover:text-[#dfb54e] focus:text-[#dfb54e] focus:outline-none">
                        Voir toute la collection
                    </a>
                </li>
            </ul>
        </div>
    @endif
</li>
