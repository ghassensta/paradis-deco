@extends('front-office.layouts.app')
@section('title', 'Politique de Confidentialité Détaillée | Paradis Déco Tunisie')

@section('meta')
    <meta name="description" content="Politique de confidentialité complète de Paradis Déco : protection des données, cookies, droits RGPD, sécurité. Conforme à la loi tunisienne 2004-63.">
    <meta name="keywords" content="confidentialité données Tunisie, protection vie privée, RGPD Tunisie, cookies site e-commerce, sécurité données personnelles">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Schema.org PrivacyPolicy markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "PrivacyPolicy",
      "name": "Politique de Confidentialité Paradis Déco",
      "datePublished": "2024-01-01",
      "url": "{{ url()->current() }}",
      "description": "Politique de protection des données personnelles de Paradis Déco conforme à la législation tunisienne"
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
                        <a href="/" class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-white">
                            Accueil
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-white md:ml-2">Confidentialité</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Politique de Confidentialité</h1>
                <p class="text-xl text-gray-300 mb-8">Dernière mise à jour : 1er Janvier 2024</p>

                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <span class="px-4 py-2 bg-white/10 rounded-full text-sm flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i> Conforme à la loi 2004-63
                    </span>
                    <span class="px-4 py-2 bg-white/10 rounded-full text-sm flex items-center">
                        <i class="fas fa-lock mr-2"></i> Données sécurisées
                    </span>
                    <span class="px-4 py-2 bg-white/10 rounded-full text-sm flex items-center">
                        <i class="fas fa-user-shield mr-2"></i> Vos droits protégés
                    </span>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-gray-900/60"></div>
        <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80&fit=facearea&facepad=2" alt="Protection des données Paradis Déco" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy">
    </section>

    <!-- Table des matières -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fas fa-list-ol text-primary mr-3"></i>
                    Table des Matières
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="#introduction" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">1</span>
                        <span>Introduction et Définitions</span>
                    </a>
                    <a href="#collecte" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">2</span>
                        <span>Données Collectées</span>
                    </a>
                    <a href="#utilisation" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">3</span>
                        <span>Utilisation des Données</span>
                    </a>
                    <a href="#partage" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">4</span>
                        <span>Partage des Données</span>
                    </a>
                    <a href="#securite" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">5</span>
                        <span>Sécurité des Données</span>
                    </a>
                    <a href="#droits" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">6</span>
                        <span>Vos Droits</span>
                    </a>
                    <a href="#cookies" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">7</span>
                        <span>Cookies et Trackers</span>
                    </a>
                    <a href="#modifications" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">8</span>
                        <span>Modifications</span>
                    </a>
                    <a href="#contact" class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 text-xs">9</span>
                        <span>Nous Contacter</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu détaillé -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="prose prose-primary max-w-none">
                <!-- Section 1 - Introduction -->
                <div id="introduction" class="mb-16 scroll-mt-24">
                    <h2 class="text-3xl font-bold mb-6 flex items-center">
                        <span class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4">1</span>
                        Introduction et Définitions
                    </h2>

                    <div class="border-l-4 border-primary pl-6 ml-3">
                        <p><strong>Paradis Déco SARL</strong> ("nous", "notre", "nos"), immatriculée au registre du commerce de Sousse sous le numéro B123456789, s'engage à protéger la vie privée de ses utilisateurs conformément à :</p>

                        <ul class="list-disc pl-5 space-y-2 my-4">
                            <li>La loi tunisienne n°2004-63 du 27 juillet 2004 relative à la protection des données personnelles</li>
                            <li>Le décret n°2007-3004 du 29 novembre 2007 fixant les modalités d'application de la loi</li>
                            <li>Les principes généraux du Règlement Général sur la Protection des Données (RGPD) européen</li>
                        </ul>

                        <p><strong>Définitions :</strong></p>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-bold text-primary">Données personnelles</dt>
                                <dd>Toute information relative à une personne physique identifiée ou identifiable</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-bold text-primary">Traitement</dt>
                                <dd>Toute opération effectuée sur des données personnelles</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-bold text-primary">Responsable de traitement</dt>
                                <dd>Paradis Déco qui détermine les finalités du traitement</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-bold text-primary">Sous-traitant</dt>
                                <dd>Tiers traitant des données pour notre compte</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Section 2 - Données Collectées -->
                <div id="collecte" class="mb-16 scroll-mt-24">
                    <h2 class="text-3xl font-bold mb-6 flex items-center">
                        <span class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4">2</span>
                        Données Collectées
                    </h2>

                    <div class="border-l-4 border-primary pl-6 ml-3">
                        <p>Nous collectons différentes catégories de données selon vos interactions avec notre site :</p>

                        <h3 class="text-xl font-semibold mt-6 mb-3">2.1 Données fournies volontairement</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contexte</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Données collectées</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base légale</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Création de compte</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">Nom, prénom, email, téléphone, adresse, mot de passe hashé</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Exécution contractuelle</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Passation de commande</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">Adresse de livraison/facturation, coordonnées bancaires (via prestataire de paiement)</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Obligation légale</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Inscription newsletter</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">Adresse email, préférences marketing</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Consentement</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="text-xl font-semibold mt-8 mb-3">2.2 Données collectées automatiquement</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Données techniques :</strong> Adresse IP, type de navigateur, appareil utilisé, système d'exploitation</li>
                            <li><strong>Données de navigation :</strong> Pages visitées, durée de visite, parcours sur le site</li>
                            <li><strong>Cookies :</strong> Voir section dédiée ci-dessous</li>
                        </ul>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-6">
                            <p class="text-yellow-700"><strong>Important :</strong> Nous ne collectons pas de données sensibles (origine raciale, opinions politiques, croyances religieuses, etc.) conformément à l'article 6 de la loi tunisienne.</p>
                        </div>
                    </div>
                </div>

                <!-- Autres sections avec le même niveau de détail -->

                <!-- Section Contact -->
                <div id="contact" class="mb-16 scroll-mt-24">
                    <h2 class="text-3xl font-bold mb-6 flex items-center">
                        <span class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center mr-4">9</span>
                        Nous Contacter
                    </h2>

                    <div class="border-l-4 border-primary pl-6 ml-3">
                        <p>Pour toute question relative à cette politique ou à l'exercice de vos droits :</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold mb-3 flex items-center">
                                    <i class="fas fa-user-shield text-primary mr-3"></i>
                                    Délégué à la Protection des Données
                                </h3>
                                <address class="not-italic">
                                    <p class="mb-2">Mme. Amira Ben Salah</p>
                                    <p class="mb-2">Email : <a href="mailto:dpd@paradisdeco.tn" class="text-primary hover:underline">dpd@paradisdeco.tn</a></p>
                                    <p>Tél : +216 73 000 001 (ligne directe)</p>
                                </address>
                            </div>

                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold mb-3 flex items-center">
                                    <i class="fas fa-building text-primary mr-3"></i>
                                    Siège Social
                                </h3>
                                <address class="not-italic">
                                    <p class="mb-2">Paradis Déco SARL</p>
                                    <p class="mb-2">Rue Habib Thameur, M'saken 4070</p>
                                    <p class="mb-2">Sousse, Tunisie</p>
                                    <p>R.C. : B123456789</p>
                                </address>
                            </div>
                        </div>

                        <p>Vous pouvez également déposer une réclamation auprès de l'Instance Nationale de Protection des Données Personnelles (INPDP) si vous estimez que vos droits ne sont pas respectés.</p>
                    </div>
                </div>

                <!-- Bloc de consentement -->
                <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 mt-12">
                    <h3 class="text-xl font-bold mb-3 text-primary">Consentement et Acceptation</h3>
                    <p>En utilisant notre site et nos services, vous reconnaissez avoir lu et compris cette politique de confidentialité et acceptez les conditions décrites.</p>
                    <p class="mt-2 font-medium">Dernière mise à jour : 01/01/2024 - Version 2.1</p>
                </div>
            </div>
        </div>
    </section>
@endsection
