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

    <!-- Robots -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
@endsection

@section('content')
    <div class="container mx-auto px-4 py-12 md:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mb-8 text-sm text-gray-600">
            <ol class="flex items-center space-x-2">
                <li><a href="/" class="hover:text-primary transition">Accueil</a></li>
                <li>/</li>
                <li><a href="{{ route('allinspirations') }}" class="hover:text-primary transition">Inspirations</a></li>
                <li>/</li>
                <li class="text-gray-900 font-medium">{{ Str::limit($inspiration->title, 30) }}</li>
            </ol>
        </nav>

        <!-- Main Content -->
        <article class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <header class="mb-12 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    {{ $inspiration->title }}
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ $inspiration->resume ?? 'Pas de résumé disponible.' }}
                </p>
            </header>

            <!-- Featured Image -->
            <div class="mb-12 rounded-xl overflow-hidden shadow-lg">
                @if ($inspiration->image)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset('storage/' . $inspiration->image) }}">
                        <img src="{{ asset('storage/' . $inspiration->image) }}" alt="{{ $inspiration->title }}"
                            class="w-full h-auto max-h-[500px] object-cover" loading="lazy">
                    </a>
                @else
                    <img src="{{ asset('images/placeholder.jpg') }}" alt="Placeholder"
                        class="w-full h-auto max-h-[500px] object-cover" loading="lazy">
                @endif
            </div>

            <!-- Description Content -->
            <div class="prose prose-lg max-w-none text-gray-700 mb-12">
                {!! $inspiration->description ?? '<p>Pas de description disponible.</p>' !!}
            </div>

            <!-- Gallery Section -->
            @if ($inspiration->gallery && count($inspiration->gallery) > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Galerie Photos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($inspiration->gallery as $image)
                            <div class="rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset('storage/' . $image) }}">
                                    <img src="{{ asset('storage/' . $image) }}"
                                        alt="{{ $inspiration->title }} - Image {{ $loop->iteration }}"
                                        class="w-full h-64 object-cover" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Related Inspirations -->
            @if ($relatedInspirations && $relatedInspirations->count() > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">D'autres inspirations qui pourraient vous plaire</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($relatedInspirations as $related)
                            <div class="group rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                                <a href="{{ route('preview-inspiration', $related->slug) }}" class="block">
                                    <div class="relative overflow-hidden h-64">
                                        <img src="{{ $related->image ? asset('storage/' . $related->image) : asset('images/placeholder.jpg') }}"
                                            alt="{{ $related->title }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            loading="lazy">
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-primary transition">
                                            {{ $related->title }}
                                        </h3>
                                        <p class="text-gray-600 line-clamp-2">
                                            {{ $related->resume ?? 'Pas de résumé disponible.' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">

                </div>
            </div>
        </article>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update modal image source when opened
        document.addEventListener('DOMContentLoaded', function () {
            const imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imageSrc = button.getAttribute('data-image');
                const modalImage = imageModal.querySelector('#modalImage');
                modalImage.src = imageSrc;
            });

            // Hide Quill.js tooltip
            const quillTooltips = document.querySelectorAll('.ql-tooltip.ql-hidden');
            quillTooltips.forEach(tooltip => {
                tooltip.style.display = 'none';
            });
        });
    </script>
@endsection
