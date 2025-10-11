@extends('front-office.layouts.app')
@section('title', 'FAQ Paradis Déco - Questions/Réponses Détaillées | Boutique Déco Sousse')

@section('meta')
    <meta name="description"
        content="FAQ complète de Paradis Déco : 50+ questions/réponses sur commandes, livraisons, paiements, retours, garanties et services. Boutique déco à M'saken, Sousse.">
    <meta name="keywords"
        content="FAQ déco Tunisie, questions boutique en ligne, livraison décoration Sousse, retour produits déco, paiement sécurisé Tunisie">
    <!-- Schema.org FAQ markup -->
    <link rel="canonical" href="{{ url()->current() }}">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Comment passer commande sur Paradis Déco ?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "1. Sélectionnez vos articles 2. Ajoutez au panier 3. Validez votre panier 4. Choisissez la livraison 5. Sélectionnez le paiement 6. Confirmez la commande"
          }
        }
        // Ajouter d'autres questions/réponses...
      ]
    }
    </script>
@endsection

@section('content')
    <!-- Hero Section améliorée -->
    <section class="relative py-24 bg-gray-900 text-white overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/"
                            class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-white">
                            Accueil
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-white md:ml-2">FAQ</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Centre d'Aide Paradis Déco</h1>
                <p class="text-xl text-gray-300 mb-8">Trouvez des réponses détaillées à toutes vos questions en moins de 2
                    minutes</p>

                <!-- Barre de recherche FAQ -->
                <div class="max-w-2xl mx-auto mt-8">
                    <form class="relative">
                        <input type="text" id="faq-search" placeholder="Rechercher dans la FAQ..."
                            class="w-full px-5 py-4 rounded-lg border-0 focus:ring-2 focus:ring-primary text-gray-900">
                        <button type="submit"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-primary">
                            <i class="fas fa-search text-xl"></i>
                        </button>
                    </form>
                    <p class="text-sm text-gray-400 mt-2">Exemples : "retour produit", "délai livraison", "garantie"</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-gray-900/60"></div>
        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80&fit=facearea&facepad=2"
            alt="Centre d'aide Paradis Déco" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy">
    </section>

    <!-- Contenu FAQ enrichi -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Navigation latérale -->
                <div class="md:col-span-1 hidden md:block">
                    <div class="sticky top-24">
                        <h3 class="text-lg font-bold mb-4 text-gray-900">Catégories</h3>
                        <nav class="space-y-2">
                            <a href="#commandes"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Commandes</a>
                            <a href="#paiements"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Paiements</a>
                            <a href="#livraisons"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Livraisons</a>
                            <a href="#retours"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Retours
                                & Échanges</a>
                            <a href="#produits"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Produits</a>
                            <a href="#compte"
                                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-primary transition">Compte
                                Client</a>
                        </nav>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-900">Questions Populaires</h3>
                            <ul class="space-y-3">
                                <li><a href="#delai-livraison" class="text-primary hover:underline flex items-start">
                                        <span
                                            class="bg-primary/10 text-primary rounded-full w-6 h-6 flex items-center justify-center mr-2 text-xs">1</span>
                                        Délais de livraison ?
                                    </a></li>
                                <li><a href="#retour-produit"
                                        class="text-gray-700 hover:text-primary hover:underline flex items-start">
                                        <span
                                            class="bg-gray-200 rounded-full w-6 h-6 flex items-center justify-center mr-2 text-xs">2</span>
                                        Comment retourner un produit ?
                                    </a></li>
                                <li><a href="#showroom"
                                        class="text-gray-700 hover:text-primary hover:underline flex items-start">
                                        <span
                                            class="bg-gray-200 rounded-full w-6 h-6 flex items-center justify-center mr-2 text-xs">3</span>
                                        Visiter le showroom ?
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="md:col-span-3">
                    <!-- Section Commandes enrichie -->
                    <div id="commandes" class="mb-16 scroll-mt-24">
                        <h2 class="text-3xl font-bold mb-8 pb-2 border-b border-gray-200 flex items-center">
                            <i class="fas fa-shopping-cart text-primary mr-3"></i>
                            Commandes
                        </h2>

                        <div class="space-y-6">
                            <!-- Question 1 -->
                            <div
                                class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-md">
                                <button
                                    class="faq-question w-full flex justify-between items-center p-6 text-left font-medium bg-gray-50 hover:bg-gray-100 transition">
                                    <span class="text-lg">Comment passer commande sur votre site ?</span>
                                    <i class="fas fa-chevron-down transition-transform duration-300 ml-4"></i>
                                </button>
                                <div class="faq-answer p-6 hidden bg-white">
                                    <div class="prose prose-primary max-w-none">
                                        <p>Pour commander sur Paradis Déco, suivez ces étapes simples :</p>

                                        <ol class="list-decimal pl-5 space-y-2 my-4">
                                            <li><strong>Navigation</strong> : Parcourez nos catégories ou utilisez la barre
                                                de recherche</li>
                                            <li><strong>Sélection</strong> : Cliquez sur "Voir les détails" pour chaque
                                                produit qui vous intéresse</li>
                                            <li><strong>Personnalisation</strong> : Sélectionnez les options disponibles
                                                (couleur, taille, etc.)</li>
                                            <li><strong>Ajout au panier</strong> : Cliquez sur "Ajouter au panier" (vous
                                                pouvez continuer vos achats)</li>
                                            <li><strong>Validation</strong> : Cliquez sur l'icône panier puis "Passer la
                                                commande"</li>
                                            <li><strong>Connexion</strong> : Identifiez-vous ou créez un compte (ou
                                                commandez en invité)</li>
                                            <li><strong>Livraison</strong> : Sélectionnez votre adresse et le mode de
                                                livraison</li>
                                            <li><strong>Paiement</strong> : Choisissez votre moyen de paiement sécurisé</li>
                                            <li><strong>Confirmation</strong> : Validez et recevez un email de confirmation
                                            </li>
                                        </ol>

                                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 my-4">
                                            <p class="text-blue-700"><strong>Astuce :</strong> Créez un compte pour
                                                sauvegarder vos informations et suivre facilement vos commandes.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Question 2 avec tableau -->
                            <div
                                class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-md">
                                <button
                                    class="faq-question w-full flex justify-between items-center p-6 text-left font-medium bg-gray-50 hover:bg-gray-100 transition">
                                    <span class="text-lg">Comment modifier ou annuler une commande ?</span>
                                    <i class="fas fa-chevron-down transition-transform duration-300 ml-4"></i>
                                </button>
                                <div class="faq-answer p-6 hidden bg-white">
                                    <div class="prose prose-primary max-w-none">
                                        <p>La modification ou l'annulation d'une commande dépend de son statut :</p>

                                        <div class="overflow-x-auto my-4">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Statut</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Modification possible</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Annulation possible</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Action requise</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr>
                                                        <td
                                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            En attente</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Oui
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Oui
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            Contactez-nous par téléphone</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            En préparation</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Non
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Sous
                                                            conditions</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            Urgence : appelez le 73 000 000</td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            Expédiée</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Non
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Non
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            Procédez au retour après réception</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <p class="mt-4"><strong>Procédure d'annulation :</strong></p>
                                        <ol class="list-decimal pl-5 space-y-2">
                                            <li>Connectez-vous à votre compte</li>
                                            <li>Allez dans "Mes commandes"</li>
                                            <li>Sélectionnez la commande concernée</li>
                                            <li>Cliquez sur "Demander l'annulation"</li>
                                            <li>Notre service client vous contactera sous 2h</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Autres questions... -->
                        </div>
                    </div>

                    <!-- Autres sections (Paiements, Livraisons, etc.) avec le même niveau de détail -->

                    <!-- Section Contact renforcée -->
                    <div class="mt-16 bg-gradient-to-r from-primary to-primary-dark rounded-xl overflow-hidden shadow-lg">
                        <div class="p-8 md:p-12 text-white">
                            <div class="md:flex items-center justify-between">
                                <div class="md:w-2/3 mb-6 md:mb-0">
                                    <h3 class="text-2xl font-bold mb-3">Besoin d'aide supplémentaire ?</h3>
                                    <p class="text-primary-100">Notre équipe client est disponible 7j/7 pour répondre à vos
                                        questions spécifiques.</p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="{{ route('contact') }}"
                                        class="bg-white text-primary font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-300 text-center">
                                        <i class="fas fa-envelope mr-2"></i> Formulaire de contact
                                    </a>
                                    <a href="tel:+21673000000"
                                        class="bg-primary-800 text-white font-bold py-3 px-6 rounded-lg hover:bg-primary-700 transition duration-300 text-center">
                                        <i class="fas fa-phone-alt mr-2"></i> Appeler maintenant
                                    </a>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="flex items-start">
                                    <div class="bg-primary-700 rounded-full p-3 mr-4">
                                        <i class="fas fa-map-marker-alt text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1">Showroom</h4>
                                        <p class="text-primary-100 text-sm">Rue Habib Thameur<br>M'saken 4070, Sousse</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="bg-primary-700 rounded-full p-3 mr-4">
                                        <i class="fas fa-clock text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1">Horaires</h4>
                                        <p class="text-primary-100 text-sm">Lun-Sam : 9h-19h<br>Dimanche : Fermé</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="bg-primary-700 rounded-full p-3 mr-4">
                                        <i class="fas fa-headset text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1">Support</h4>
                                        <p class="text-primary-100 text-sm">+216 73 000 000<br>contact@paradisdeco.tn</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        // Script FAQ amélioré
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                const icon = button.querySelector('i');

                // Fermer les autres réponses
                document.querySelectorAll('.faq-answer').forEach(item => {
                    if (item !== answer) {
                        item.classList.add('hidden');
                        item.previousElementSibling.querySelector('i').classList.remove(
                            'rotate-180');
                    }
                });

                // Basculer l'état actuel
                answer.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');

                // Scroll doux vers la question
                if (!answer.classList.contains('hidden')) {
                    setTimeout(() => {
                        answer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }, 100);
                }
            });
        });

        // Fonctionnalité de recherche FAQ
        document.getElementById('faq-search').addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();

                    if (searchTerm.length > 2) {
                        document.querySelectorAll('.faq-question').forEach(question => {
                                const questionText = question.textContent.toLowerCase();
                                const answer = question.nextElementSibling;

                                if (questionText.includes(searchTerm)) {
                                        question.parentElement.style.display = 'block';
                                        question.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'nearest'
                                        });
                                    } else {
                                        question.parentElement.style.display = 'none';
                                    }
                                });
                        }
                        else {
                            document.querySelectorAll('.faq-question').forEach(question => {
                                question.parentElement.style.display = 'block';
                            });
                        }
                    });
    </script>
@endsection
