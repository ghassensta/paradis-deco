@extends('front-office.layouts.app')
@section('title', 'Mentions Légales - Paradis Déco Tunisie | Informations Légales')

@section('meta')
    <meta name="description" content="Mentions légales de Paradis Déco Tunisie : informations sur l'éditeur, hébergement, protection des données et conditions d'utilisation du site.">
    <meta name="keywords" content="mentions légales Tunisie, informations légales boutique en ligne, éditeur site e-commerce Tunisie, hébergement site web Tunisie">
    <link rel="canonical" href="{{ url()->current() }}">

    @endsection

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gray-800 text-white py-16 md:py-24">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <nav class="flex justify-center mb-6" aria-label="Fil d'Ariane">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="/" class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-white">
                                Accueil
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-sm font-medium text-white md:ml-2">Mentions Légales</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <h1 class="text-3xl md:text-4xl font-bold mb-4">Mentions Légales</h1>
                <p class="text-xl text-gray-300">Informations légales de Paradis Déco</p>
            </div>
        </div>
    </section>

    <!-- Contenu -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="prose prose-lg max-w-none">
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">1. Éditeur du Site</h2>
                        <p><strong>Paradis Déco SARL</strong>, société à responsabilité limitée au capital de 50 000 TND</p>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li><strong>Siège social :</strong> Rue Habib Thameur, M'saken 4070, Sousse, Tunisie</li>
                            <li><strong>Registre de commerce :</strong> B123456789</li>
                            <li><strong>Numéro fiscal :</strong> 12345678A</li>
                            <li><strong>Téléphone :</strong> +216 73 000 000</li>
                            <li><strong>Email :</strong> contact@paradisdeco.tn</li>
                            <li><strong>Directeur de publication :</strong> M. Ahmed Ben Salah</li>
                        </ul>
                    </div>

                    <div class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">2. Hébergement</h2>
                        <p>Le site paradisdeco.tn est hébergé par :</p>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li><strong>Nom :</strong> Tunisie Hébergement</li>
                            <li><strong>Adresse :</strong> Centre Urbain Nord, Tunis, Tunisie</li>
                            <li><strong>Téléphone :</strong> +216 70 000 000</li>
                            <li><strong>Site web :</strong> www.tunisie-hebergement.tn</li>
                        </ul>
                    </div>

                    <div class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">3. Propriété Intellectuelle</h2>
                        <p>Tous les éléments du site (textes, images, vidéos, logos, etc.) sont la propriété exclusive de Paradis Déco ou de ses partenaires et sont protégés par les lois tunisiennes et internationales sur la propriété intellectuelle.</p>
                        <p class="mt-2">Toute reproduction, modification ou exploitation commerciale sans autorisation écrite préalable est strictement interdite.</p>
                    </div>

                    <div class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">4. Données Personnelles</h2>
                        <p>Conformément à la loi tunisienne n°2004-63 du 27 juillet 2004 relative à la protection des données personnelles, vous disposez des droits d'accès, de rectification et d'opposition sur vos données.</p>
                        <p class="mt-2">Pour exercer ces droits ou pour toute question relative à la protection des données, vous pouvez contacter notre délégué à la protection des données à l'adresse : dpd@paradisdeco.tn</p>
                        <p class="mt-2">Consultez notre <a href="{{ route('politique-confidentialite') }}" class="text-primary-600 hover:underline">Politique de Confidentialité</a> pour plus d'informations.</p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">5. Cookies</h2>
                        <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur. Vous pouvez configurer votre navigateur pour refuser les cookies, mais certaines fonctionnalités du site pourraient ne plus fonctionner correctement.</p>
                    </div>
                </div>

                <div class="mt-12 border-t border-gray-200 pt-8 text-center">
                    <p class="text-gray-500">Dernière mise à jour : {{ date('d/m/Y', strtotime($lastUpdated ?? '2024-01-01')) }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
