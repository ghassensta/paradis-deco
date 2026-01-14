@extends('front-office.layouts.app')

{{-- ===== TITRE ===== --}}
@section('title', 'Contact | Paradis Déco - Boutique Déco Tunisie | Décoration Intérieure Tunisie')

{{-- ===== MÉTA ===== --}}
@section('meta')
    {{-- SEO de base --}}
    <meta name="description"
          content="Besoin d’aide ou d’informations ? Contactez Paradis Déco – votre boutique de décoration intérieure en Tunisie. Notre équipe répond à vos questions sur les meubles tunisiens, tapis Kairouan, luminaires et accessoires maison." >
    <meta name="keywords"
          content="contact Paradis Déco, service client décoration Tunisie, questions boutique déco, meubles tunisiens, tapis Kairouan, luminaires Tunisie, accessoires maison Tunisie, boutique déco en ligne">
    <meta name="author" content="Paradis Déco">

    {{-- Canonical & hreflang --}}
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- Open Graph / Social --}}
    <meta property="og:locale" content="fr_TN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact | Paradis Déco">
    <meta property="og:description"
          content="Contactez l’équipe Paradis Déco pour toute question sur nos meubles, tapis et accessoires made in Tunisia. Nous sommes là pour vous aider !">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    {{-- Indexation --}}
    <meta name="robots" content="index, follow">
@endsection

@section('content')
<!-- CONTACT -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold">Contactez-Nous</h1>
            <h2 class="text-gray-600 max-w-2xl mx-auto">Nous sommes à votre écoute pour toute question ou demande d'information.</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Formulaire de contact -->
            <div class="bg-white p-8 rounded-xl shadow-md">
                <h3 class="text-xl font-bold mb-6">Envoyez-nous un message</h3>
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 mb-2">Nom complet</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    <button type="submit"
                        class="bg-gray-200/80 font-bold hover:bg-primary-dark px-6 py-3 rounded-lg transition w-full">
                        Envoyer le message
                    </button>
                </form>
            </div>

            <!-- Informations de contact -->
            <div>
                <div class="bg-white p-8 rounded-xl shadow-md mb-6">
                    <h3 class="text-xl font-bold mb-4">Nos coordonnées</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-semibold">Adresse</h4>
                                <p class="text-gray-600">{{ $config->address ?? 'Msaken,Sousse,Tunisie' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-phone-alt text-primary mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-semibold">Téléphone</h4>
                                <p class="text-gray-600">{{ $config->phone ?? '+216 99 592 125' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-primary mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-semibold">Email</h4>
                                <p class="text-gray-600">{{ $config->support_email ?? 'contact@votresite.com' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock text-primary mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-semibold">Horaires d'ouverture</h4>
                                <p class="text-gray-600">{{ $config->opening_hours ?? 'Lundi-Vendredi: 9h-18h' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Google Maps -->
                <div class="bg-white p-4 rounded-xl shadow-md">
                    <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3190.634185060771!2d10.181531315295877!3d36.80287097996172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd34ce8d66a3d1%3A0x415c1a5a4a1a6a6a!2sTunis!5e0!3m2!1sen!2stn!4v1620000000000!5m2!1sen!2stn"
                                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold">Questions Fréquentes</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Trouvez rapidement des réponses à vos questions.</p>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100">
                        <span class="font-semibold text-left">Comment puis-je passer une commande ?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden p-4">
                        <p class="text-gray-600">Vous pouvez passer commande directement sur notre site en sélectionnant les articles de votre choix et en suivant le processus de paiement sécurisé.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100">
                        <span class="font-semibold text-left">Quels sont les délais de livraison ?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden p-4">
                        <p class="text-gray-600">Les délais de livraison varient entre 2 et 5 jours ouvrés selon votre localisation et la disponibilité des produits.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100">
                        <span class="font-semibold text-left">Quelles sont les méthodes de paiement acceptées ?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden p-4">
                        <p class="text-gray-600">Nous acceptons les cartes bancaires (Visa, MasterCard), les virements bancaires et le paiement à la livraison pour certaines zones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ toggle functionality
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('i');

                content.classList.toggle('hidden');
                icon.classList.toggle('transform');
                icon.classList.toggle('rotate-180');
            });
        });
    });
</script>
@endsection
