{{--
    Accordéon FAQ, partagé fiche produit / page catégorie.
    $faqs    : collection de App\Models\Faq déjà filtrée (displayableFaqs)
    $heading : titre de la section
    $uid     : préfixe d'identifiants, unique dans la page
--}}
@if ($faqs->isNotEmpty())
    <section class="mt-12 bg-white rounded-xl shadow-sm overflow-hidden" aria-labelledby="{{ $uid }}-title">
        <div class="p-6">
            <h2 id="{{ $uid }}-title" class="text-xl md:text-2xl font-bold text-gray-900 mb-4">
                {{ $heading }}
            </h2>

            <div class="divide-y divide-gray-100 border-t border-gray-100">
                @foreach ($faqs as $faq)
                    @php $panel = $uid . '-panel-' . $faq->id; @endphp
                    <div>
                        <h3>
                            {{-- Bouton réel : accessible au clavier, sans JS bloquant. --}}
                            <button type="button"
                                    class="js-faq-toggle w-full flex items-start justify-between gap-4 py-4 text-left"
                                    aria-expanded="false"
                                    aria-controls="{{ $panel }}"
                                    id="{{ $panel }}-button">
                                <span class="font-medium text-gray-900">{{ $faq->question }}</span>
                                <svg class="js-faq-icon w-5 h-5 flex-shrink-0 mt-0.5 text-gray-400 transition-transform"
                                     fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </h3>

                        {{-- Réponse déjà nettoyée par RichText à l'enregistrement. --}}
                        <div id="{{ $panel }}"
                             role="region"
                             aria-labelledby="{{ $panel }}-button"
                             class="js-faq-panel product-description pb-5 text-gray-700"
                             hidden>
                            {!! $faq->safe_answer !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQPage : émis uniquement parce qu'il existe des FAQ réelles et visibles. --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq->answer_text,
            ],
        ])->values()->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
