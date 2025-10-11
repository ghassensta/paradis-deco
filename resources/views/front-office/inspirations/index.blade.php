@extends('front-office.layouts.app')

@section('title', $inspiration->meta_title ?? $inspiration->title . ' | Paradis Déco')

@section('meta')
    <meta name="description" content="{{ $inspiration->meta_description ?? $inspiration->resume }}">
    <meta property="og:title" content="{{ $inspiration->title }}">
    <meta property="og:description" content="{{ $inspiration->meta_description ?? $inspiration->resume }}">
    <meta property="og:image" content="{{ asset('storage/' . $inspiration->image) }}">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

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
                <li><a href="/" class="hover:text-primary transition">Inspirations</a></li>
                <li>/</li>
                <li class="text-gray-900 font-medium">{{ $inspiration->title }}</li>
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
                    {{ $inspiration->resume }}
                </p>
            </header>

            <!-- Featured Image -->
            <div class="mb-12 rounded-xl overflow-hidden shadow-lg">
                <img src="{{ asset('storage/' . $inspiration->image) }}" alt="{{ $inspiration->title }}"
                    class="w-full h-auto object-cover" loading="lazy">
            </div>

            <!-- Description Content -->
            <div class="prose prose-lg max-w-none text-gray-700 mb-12">
                {!! $inspiration->description !!}
            </div>

            <!-- Gallery Section (if you have additional images) -->
            @if ($inspiration->gallery && count($inspiration->gallery) > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Galerie Photos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($inspiration->gallery as $image)
                            <div
                                class="rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
                                <img src="{{ asset('storage/' . $image) }}"
                                    alt="{{ $inspiration->title }} - Image {{ $loop->iteration }}"
                                    class="w-full h-64 object-cover" loading="lazy">
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
                            <div
                                class="group rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                                <a href="{{ route('preview-inspiration', $related->slug) }}" class="block">
                                    <div class="relative overflow-hidden h-64">
                                        <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            loading="lazy">
                                    </div>
                                    <div class="p-6">
                                        <h3
                                            class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-primary transition">
                                            {{ $related->title }}
                                        </h3>
                                        <p class="text-gray-600 line-clamp-2">
                                            {{ $related->resume }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>
    </div>
@endsection
