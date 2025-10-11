@extends('front-office.layouts.app')
@section('title', 'Paradis Déco - À propos de Paradis Déco - Boutique Déco Tunisie | Décoration Intérieur Tunisie')

@section('meta')
    <meta name="keywords" content="décoration intérieur Tunisie, meubles tunisiens, tapis, boutique déco en ligne, artisanat tunisien, décoration moderne Tunis, accessoires déco pas chers Tunisie, boutique en ligne Tunisie, vente décoration en ligne, luminaires Tunisie, accessoires maison Tunisie">
    <meta name="author" content="Paradis Déco">
    <meta name="description" content="Paradis Déco, boutique en ligne de décoration d'intérieur à M'saken, Sousse. Découvrez notre sélection de meubles, luminaires et articles déco pour embellir votre maison en Tunisie.">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow" />
@endsection

@section('content')
    <section class="relative py-20 bg-gray-900 text-white overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Paradis Déco : Votre Boutique Déco en Ligne</h1>
                <p class="text-xl text-gray-300 mb-8">Spécialistes en décoration intérieure à M'saken, Sousse - Meubles, luminaires et accessoires maison à prix tunisiens</p>
            </div>
        </div>
        <div class="absolute inset-0 bg-black/50"></div>
        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
            alt="Boutique de décoration en ligne Tunisie - Paradis Déco" class="absolute inset-0 w-full h-full object-cover z-0">
    </section>

    <!-- NOTRE HISTOIRE -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
                        alt="Boutique de décoration en ligne Tunisie - Meubles et accessoires"
                        class="rounded-xl shadow-lg w-full h-auto object-cover min-h-[400px]">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold mb-6">Notre Histoire à M'saken</h2>
                    <div class="prose max-w-none">
                        <p><strong>Paradis Déco</strong>, <strong>boutique en ligne de décoration</strong> basée à <strong>M'saken, Sousse</strong>, apporte une touche d'élégance aux foyers tunisiens depuis 2015. Notre plateforme e-commerce spécialisée dans la <strong>décoration intérieure</strong> propose des centaines de références soigneusement sélectionnées.</p>

                        <p>Nous mettons en avant des produits de qualité avec nos collections de <strong>meubles modernes</strong>, nos <strong>luminaires design</strong> et nos <strong>accessoires déco</strong> pour toutes les pièces de la maison. Notre showroom à M'saken permet de découvrir physiquement nos collections avant d'acheter sur notre <strong>boutique en ligne</strong>.</p>

                        <p>Spécialistes des <strong>spots lumineux</strong> et <strong>luminaires</strong> de tous types, nous proposons une large gamme adaptée à tous les budgets et tous les styles, du classique au contemporain.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NOS CATÉGORIES PHARES -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Nos Collections</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Découvrez nos gammes de produits les plus appréciées</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($categories as $category)
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                    <img src="{{ $category->image_url }}"
                        alt="Catégorie {{ $category->name }}"
                        class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($category->description ?? 'Découvrez notre collection ' . $category->name, 100) }}</p>
                        <a href="{{ route('categorie.produits', $category->slug) }}" class="text-primary font-semibold">Voir la collection →</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- AVANTAGES E-COMMERCE -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Pourquoi Choisir Paradis Déco ?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Les avantages de notre boutique en ligne à M'saken</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-light rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Livraison Rapide</h3>
                    <p class="text-gray-600">Expédition sous 48h dans toute la région de Sousse et environs</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-light rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Prix Compétitifs</h3>
                    <p class="text-gray-600">Des prix adaptés au marché local avec des promotions régulières</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-light rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-store text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Showroom à M'saken</h3>
                    <p class="text-gray-600">Venez découvrir nos produits en vrai avant d'acheter</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-light rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Conseils Experts</h3>
                    <p class="text-gray-600">Notre équipe vous guide dans vos choix de décoration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- GUIDE D'ACHAT -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12">
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold mb-6">Conseils Déco par Paradis Déco</h2>
                    <div class="prose max-w-none">
                        <p>Nos experts en décoration basés à M'saken partagent leurs conseils pour aménager votre intérieur :</p>

                        <ul class="list-disc pl-5 mt-4 space-y-2">
                            <li><strong>Choisir l'éclairage parfait</strong> : spots, suspensions ou lustres selon vos pièces</li>
                            <li><strong>Agencement d'un petit espace</strong> : nos astuces pour optimiser</li>
                            <li><strong>Marier les styles déco</strong> : moderne, classique ou mixte</li>
                            <li><strong>Choix des couleurs</strong> : créer une harmonie dans votre intérieur</li>
                        </ul>

                        <p class="mt-4">Contactez-nous pour des conseils personnalisés sur votre projet déco.</p>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <img src="https://images.unsplash.com/photo-1600121848594-d8644e57abab?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                        alt="Conseils décoration intérieur - Paradis Déco M'saken"
                        class="rounded-xl shadow-lg w-full h-auto object-cover min-h-[300px]">
                </div>
            </div>
        </div>
    </section>

    <!-- TEMOIGNAGES -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Nos Clients Témoignent</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Ce que disent nos clients de la région de Sousse</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($avis as $avi)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-2">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < $avi->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        @if($avi->product)
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">Acheté : {{ $avi->product->name }}</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-4">"{{ $avi->comment }}"</p>
                    <div class="font-medium">
                        - {{ $avi->name }}
                        @if($avi->location)
                        , {{ $avi->location }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA BOUTIQUE -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-6">Prêt à Embellir Votre Intérieur ?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Découvrez notre catalogue complet de meubles et accessoires déco sur notre boutique en ligne</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('allproduits') }}" class="bg-white text-primary font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition duration-300">Visiter la Boutique</a>
                <a href="{{ route('contact') }}" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-lg hover:bg-white hover:text-primary transition duration-300">Nous Contacter</a>
            </div>
        </div>
    </section>
@endsection
