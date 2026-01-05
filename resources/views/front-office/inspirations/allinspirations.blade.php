@extends('front-office.layouts.app')

@section('title', 'Toutes nos inspirations | Paradis Déco')

@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Paradis Déco : découvrez notre collection complète d'inspirations pour sublimer votre intérieur en Tunisie.">
    <meta name="keywords" content="inspirations déco Tunisie, décoration intérieure, idées déco, artisanat tunisien, design intérieur, inspirations maison">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="theme-color" content="#FDD835">
    <meta name="author" content="Paradis Déco">
    <meta name="publisher" content="Paradis Déco">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                Nos Inspirations Déco
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-6 sm:mb-8">
                Trouvez l'inspiration pour transformer votre intérieur avec nos idées uniques et tendances.
            </p>
            <div class="w-20 sm:w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 sm:py-12 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Breadcrumb -->
            <div class="mb-6 sm:mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="/" class="inline-flex items-center text-xs sm:text-sm font-medium text-gray-600 hover:text-primary transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Accueil
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-xs sm:text-sm font-medium text-gray-900 md:ml-2">Inspirations</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Inspirations Count -->
            <div class="mb-4 sm:mb-6 flex justify-between items-center">
                <p class="text-sm sm:text-base text-gray-600">
                    <span class="font-medium text-gray-900">{{ $inspirations->total() }}</span> inspirations disponibles
                </p>
            </div>

            <!-- Inspirations Grid -->
            <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @forelse($inspirations as $inspiration)
                    <article class="bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-lg border border-gray-100 hover:border-gray-200">
                        <!-- Image Section -->
                        <div class="relative overflow-hidden aspect-square">
                            <a href="{{ route('preview-inspiration', $inspiration->slug) }}" class="block h-full">
                                <img src="{{ $inspiration->image ? asset('storage/' . $inspiration->image) : asset('images/placeholder.jpg') }}"
                                    alt="{{ $inspiration->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy" />
                            </a>
                            <!-- Badge for New Inspirations -->
                            @if($inspiration->created_at->diffInDays(now()) < 30)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-[#228B22] text-white text-xs font-semibold px-2 py-1 rounded-full uppercase shadow-sm">Nouveau</span>
                                </div>
                            @endif
                        </div>

                        <!-- Content Section -->
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-900 hover:text-primary transition-colors mb-1 line-clamp-2">
                                <a href="{{ route('preview-inspiration', $inspiration->slug) }}">{{ $inspiration->title }}</a>
                            </h3>
                            <p class="text-gray-500 text-xs sm:text-sm line-clamp-2 mb-2">{!! $inspiration->resume ?? 'Pas de résumé disponible.' !!}</p>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="max-w-md mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-medium text-gray-700 mb-2">Aucune inspiration trouvée</h3>
                            <p class="text-gray-500 mb-6">Revenez bientôt pour découvrir de nouvelles inspirations.</p>
                            <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition font-medium">
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($inspirations->hasPages())
                <div class="mt-8 sm:mt-12 border-t border-gray-100 pt-8">
                    {{ $inspirations->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-12 sm:py-16 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 sm:px-6 max-w-4xl text-center">
            <div class="bg-white p-8 sm:p-10 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Restez informé</h2>
                <p class="text-gray-600 mb-6 max-w-lg mx-auto">
                    Abonnez-vous à notre newsletter pour recevoir en exclusivité nos nouveautés et inspirations.
                </p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Votre email" class="flex-1 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm">
                    <button type="submit" class="bg-black text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition text-sm shadow-sm hover:shadow-md">
                        S'abonner
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        /* Inspiration hover effect */
        article {
            transition: all 0.3s ease;
        }
        article:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Badges */
        [class^="bg-"]:not(.bg-white):not(.bg-gray-50) {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Line clamping for text */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Pagination active state */
        .pagination .active .page-link {
            background-color: #000;
            border-color: #000;
            color: white;
            font-weight: 500;
        }
        .pagination .page-link {
            color: #000;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }
        .pagination .page-link:hover {
            background-color: #f3f4f6;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>
@endsection

@section('js')
    <script>
        // Hide Quill.js tooltip
        document.addEventListener('DOMContentLoaded', function () {
            const quillTooltips = document.querySelectorAll('.ql-tooltip.ql-hidden');
            quillTooltips.forEach(tooltip => {
                tooltip.style.display = 'none';
            });
        });
    </script>
@endsection
