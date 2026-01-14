@extends('front-office.layouts.app')

@section('title', $inspiration->meta_title ?? $inspiration->title . ' | Paradis Déco')

@section('meta')
    <meta name="description" content="{{ $inspiration->meta_description ?? $inspiration->resume ?? 'Découvrez cette inspiration sur Paradis Déco.' }}">
    <meta property="og:title" content="{{ $inspiration->meta_title ?? $inspiration->title }}">
    <meta property="og:description" content="{{ $inspiration->meta_description ?? $inspiration->resume ?? 'Découvrez cette inspiration sur Paradis Déco.' }}">
    <meta property="og:image" content="{{ $inspiration->image ? asset('storage/' . $inspiration->image) : asset('images/placeholder.jpg') }}">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
@endsection

@section('css')
    <style>
        /* Nettoyage très agressif des résidus Quill.js */
        .ql-tooltip,
        .ql-hidden,
        .ql-toolbar,
        .ql-bubble,
        .ql-cursor,
        .ql-link-tooltip,
        .ql-editing,
        .ql-blank::before {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Force la désactivation de l'édition */
        [contenteditable] {
            -webkit-user-modify: read-only !important;
            -moz-user-modify: read-only !important;
            user-modify: read-only !important;
            pointer-events: auto !important;
            cursor: text !important;
        }

        /* Liens normaux et cliquables */
        .prose a,
        .content a {
            pointer-events: auto !important;
            cursor: pointer !important;
            color: #2563eb;
            text-decoration: underline;
            transition: all 0.2s ease;
        }

        .prose a:hover,
        .content a:hover {
            color: #1d4ed8;
            text-decoration-thickness: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-12 md:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mb-8 text-sm text-gray-600">
            <ol class="flex items-center space-x-2">
                <li><a href="/" class="hover:text-primary transition">Accueil</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('allinspirations') }}" class="hover:text-primary transition">Inspirations</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">{{ Str::limit($inspiration->title, 35) }}</li>
            </ol>
        </nav>

        <article class="max-w-5xl mx-auto">
            <!-- Titre + Résumé -->
            <header class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-5">
                    {{ $inspiration->title }}
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    {{ $inspiration->resume ?? 'Pas de résumé disponible pour cette inspiration.' }}
                </p>
            </header>

            <!-- Image principale -->
            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
                @if ($inspiration->image)
                    <a href="#"
                       data-bs-toggle="modal"
                       data-bs-target="#imageModal"
                       data-image="{{ asset('storage/' . $inspiration->image) }}"
                       class="block">
                        <img src="{{ asset('storage/' . $inspiration->image) }}"
                             alt="{{ $inspiration->title }}"
                             class="w-full h-auto max-h-[580px] object-cover transition-transform hover:scale-[1.02] duration-500"
                             loading="lazy">
                    </a>
                @else
                    <img src="{{ asset('images/placeholder.jpg') }}"
                         alt="Image d'inspiration décoration"
                         class="w-full h-auto max-h-[580px] object-cover"
                         loading="lazy">
                @endif
            </div>

            <!-- Contenu principal (zone nettoyée) -->
            <div class="content prose prose-lg prose-headings:text-gray-900 prose-a:text-primary max-w-none mb-16">
                {!! $inspiration->description ?? '<p class="text-gray-500 italic">Aucune description détaillée disponible pour le moment.</p>' !!}
            </div>

            <!-- Galerie -->
            @if ($inspiration->gallery && count($inspiration->gallery) > 0)
                <section class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center md:text-left">Galerie d'inspiration</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($inspiration->gallery as $image)
                            <div class="group rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300">
                                <a href="#"
                                   data-bs-toggle="modal"
                                   data-bs-target="#imageModal"
                                   data-image="{{ asset('storage/' . $image) }}">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $inspiration->title }} - photo {{ $loop->iteration }}"
                                         class="w-full aspect-[4/3] object-cover transition-transform duration-500 group-hover:scale-110"
                                         loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Inspirations similaires -->
            @if ($relatedInspirations && $relatedInspirations->count() > 0)
                <section class="mt-20">
                    <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center">Vous aimerez peut-être aussi...</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($relatedInspirations as $related)
                            <a href="{{ route('preview-inspiration', $related->slug) }}"
                               class="group block rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 bg-white">
                                <div class="relative overflow-hidden aspect-[4/3]">
                                    <img src="{{ $related->image ? asset('storage/' . $related->image) : asset('images/placeholder.jpg') }}"
                                         alt="{{ $related->title }}"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                         loading="lazy">
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-primary transition-colors">
                                        {{ $related->title }}
                                    </h3>
                                    <p class="text-gray-600 line-clamp-2">
                                        {{ $related->resume ?? 'Inspiration décoration intérieure' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        </article>
    </div>

    <!-- Modal Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <!-- Contenu injecté dynamiquement par JS -->
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Gestion modal image
            const imageModal = document.getElementById('imageModal');

            if (imageModal) {
                imageModal.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;
                    const imageSrc = trigger.getAttribute('data-image');

                    const modalDialog = imageModal.querySelector('.modal-dialog');

                    modalDialog.innerHTML = `
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <img src="${imageSrc}"
                                     alt="Image agrandie"
                                     class="w-full max-h-[90vh] object-contain mx-auto rounded-lg shadow-2xl">
                            </div>
                        </div>
                    `;
                });
            }

            // ─── SUPPRESSION DE contenteditable + nettoyage résidus Quill ───
            document.querySelectorAll('[contenteditable]').forEach(el => {
                el.removeAttribute('contenteditable');
                el.style.userSelect = 'text';
                el.style.cursor = 'text';
                el.style.webkitUserModify = 'read-only';
            });

            // Nettoyage supplémentaire des éléments Quill résiduels
            setTimeout(() => {
                document.querySelectorAll('.ql-tooltip, .ql-hidden, .ql-toolbar, .ql-bubble, .ql-cursor, .ql-editor.ql-blank::before')
                    .forEach(el => el.remove());
            }, 800);
        });
    </script>
@endsection
