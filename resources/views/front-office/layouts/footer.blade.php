@php
    $navCategories = $navCategories ?? collect();
    $reseaux = array_filter([
        'facebook' => $config->facebook_url ?? null,
        'instagram' => $config->instagram_url ?? null,
    ]);
    $navigation = [
        ['url' => route('welcome'), 'label' => 'Accueil'],
        ['url' => route('allproduits'), 'label' => 'Tous les produits'],
        ['url' => route('allinspirations'), 'label' => 'Inspirations'],
        ['url' => route('about'), 'label' => 'À propos'],
        ['url' => route('contact'), 'label' => 'Contact'],
        ['url' => route('faq'), 'label' => 'FAQ'],
    ];
@endphp

<footer class="border-t border-white/5 bg-[#1c1b19] text-gray-300">
    <div class="container mx-auto px-5 py-14 sm:px-6 md:py-16">

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">

            {{-- Marque --}}
            <div class="lg:col-span-4">
                <a href="{{ route('welcome') }}" class="inline-block" aria-label="{{ $config->site_name ?? 'Paradis Déco' }} — accueil">
                    <img src="{{ $config->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/img/cover-image-removebg-preview.png') }}"
                         width="80" height="100" alt="Logo-{{ $config->site_name }}"
                         class="h-12 w-auto brightness-0 invert" loading="lazy" decoding="async">
                </a>

                <p class="mt-5 max-w-sm text-sm leading-relaxed text-gray-400">
                    {{ $config->meta_description ?? 'Boutique de décoration tunisienne en ligne.' }}
                </p>

                @if ($reseaux)
                    <div class="mt-6 flex items-center gap-3">
                        @if (!empty($reseaux['facebook']))
                            <a href="{{ $reseaux['facebook'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 text-gray-400 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e]"
                               aria-label="Facebook">
                                <i class="fab fa-facebook-f text-sm" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if (!empty($reseaux['instagram']))
                            <a href="{{ $reseaux['instagram'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 text-gray-400 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e]"
                               aria-label="Instagram">
                                <i class="fab fa-instagram text-sm" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Collections : mêmes catégories que la navigation, liens crawlables. --}}
            @if ($navCategories->isNotEmpty())
                <nav class="lg:col-span-3" aria-labelledby="footer-collections">
                    <h2 id="footer-collections" class="mb-4 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">Nos collections</h2>
                    <ul class="space-y-2.5">
                        @foreach ($navCategories as $categorie)
                            <li>
                                <a href="{{ $categorie->url }}" class="text-sm text-gray-400 transition-colors hover:text-white">
                                    {{ $categorie->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            {{-- Navigation --}}
            <nav class="lg:col-span-2" aria-labelledby="footer-navigation">
                <h2 id="footer-navigation" class="mb-4 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">Navigation</h2>
                <ul class="space-y-2.5">
                    @foreach ($navigation as $lien)
                        <li>
                            <a href="{{ $lien['url'] }}" class="text-sm text-gray-400 transition-colors hover:text-white">
                                {{ $lien['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Contact : uniquement ce qui est renseigné en administration. --}}
            <div class="lg:col-span-3">
                <h2 class="mb-4 text-[11px] uppercase tracking-[0.2em] text-[#dfb54e]">Nous contacter</h2>
                <ul class="space-y-3 text-sm">
                    @if ($config->support_email)
                        <li class="flex items-start gap-3">
                            <i class="fas fa-envelope mt-1 w-4 shrink-0 text-gray-500" aria-hidden="true"></i>
                            <a href="mailto:{{ $config->support_email }}" class="text-gray-400 transition-colors hover:text-white">
                                {{ $config->support_email }}
                            </a>
                        </li>
                    @endif
                    @if ($config->support_phone)
                        <li class="flex items-start gap-3">
                            <i class="fas fa-phone mt-1 w-4 shrink-0 text-gray-500" aria-hidden="true"></i>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $config->support_phone) }}" class="text-gray-400 transition-colors hover:text-white">
                                {{ $config->support_phone }}
                            </a>
                        </li>
                    @endif
                    @if ($config->address)
                        <li class="flex items-start gap-3">
                            <i class="fas fa-location-dot mt-1 w-4 shrink-0 text-gray-500" aria-hidden="true"></i>
                            <span class="text-gray-400">{{ $config->address }}</span>
                        </li>
                    @endif
                    @if ($config->working_hours)
                        <li class="flex items-start gap-3">
                            <i class="fas fa-clock mt-1 w-4 shrink-0 text-gray-500" aria-hidden="true"></i>
                            <span class="text-gray-400">{{ $config->working_hours }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Barre basse --}}
        <div class="mt-12 flex flex-col items-center gap-5 border-t border-white/10 pt-6 md:flex-row md:justify-between">
            <p class="order-2 text-center text-xs text-gray-500 md:order-1 md:text-left">
                &copy; {{ date('Y') }} {{ $config->site_name ?? 'Paradis Déco' }}. Tous droits réservés.
            </p>

            <div class="order-1 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 md:order-2">
                <a href="{{ route('politique-confidentialite') }}" class="text-xs text-gray-400 transition-colors hover:text-white">
                    Politique de confidentialité
                </a>
                <a href="{{ route('mentions-legales') }}" class="text-xs text-gray-400 transition-colors hover:text-white">
                    Mentions légales
                </a>
                <a href="{{ route('sitemap') }}" class="text-xs text-gray-400 transition-colors hover:text-white">
                    Plan du site
                </a>
                <a href="https://wisecode.tn" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-full border border-white/10 px-3 py-1 text-xs text-gray-400 transition-colors hover:border-[#dfb54e] hover:text-[#dfb54e]"
                   title="Site créé par wise-code">
                    <i class="fas fa-rocket text-[10px]" aria-hidden="true"></i>
                    wise-code
                </a>
            </div>
        </div>
    </div>
</footer>
