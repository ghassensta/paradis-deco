<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-xl font-bold mb-4">{{ $config->site_name ?? 'Paradis Déco' }}</h3>
                <p class="text-gray-400 mb-4">
                    {{ $config->meta_description ?? 'Boutique de décoration tunisienne en ligne' }}</p>

            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-xl font-bold mb-4">Navigation</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">Accueil</a></li>
                    <li><a href="{{ route('allproduits') }}"
                            class="text-gray-400 hover:text-white transition">Produits</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition">À propos</a>
                    </li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition">Contact</a>
                    </li>
                    <li><a href="{{ route('faq') }}" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-xl font-bold mb-4">Contact</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-2 text-gray-400"></i>
                        <a href="mailto:{{ $config->support_email ?? 'contact@paradisdeco.tn' }}"
                            class="text-gray-400 hover:text-white transition">
                            {{ $config->support_email ?? 'contact@paradisdeco.tn' }}
                        </a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-2 text-gray-400"></i>
                        <a href="tel:{{ $config->support_phone ?? '+21673000000' }}"
                            class="text-gray-400 hover:text-white transition">
                            {{ $config->support_phone ?? '+216 73 000 000' }}
                        </a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400"></i>
                        <span class="text-gray-400">
                            {{ $config->address ?? 'Rue Habib Thameur, M\'saken 4070, Sousse, Tunisie' }}
                        </span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-2 text-gray-400"></i>
                        <span class="text-gray-400">
                            {{ $config->working_hours ?? 'Lun-Sam : 9h-19h' }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Legal & Social -->
            <div>
                <h3 class="text-xl font-bold mb-4">Suivez-nous</h3>
                <div class="flex space-x-4 mb-6">
                    <a href="{{ $config->facebook_url ?? 'https://www.facebook.com/share/1EDbexpcnp/' }}"
                        class="text-gray-400 hover:text-white transition" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="{{ $config->instagram_url ?? 'https://www.instagram.com/paradisdeco_?igsh=MWpsdzRwNWF3cDIxaw==' }}"
                        class="text-gray-400 hover:text-white transition" target="_blank" rel="noopener">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>

                </div>

                <h3 class="text-xl font-bold mb-4">Légal</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('politique-confidentialite') }}"
                            class="text-gray-400 hover:text-white transition">
                            Politique de confidentialité
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mentions-legales') }}" class="text-gray-400 hover:text-white transition">
                            Mentions légales
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-gray-700 pt-6 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 mb-4 md:mb-0 text-center md:text-left">
                &copy; {{ date('Y') }} {{ $config->site_name ?? 'Paradis Déco' }}. Tous droits réservés.
            </p>

            <div class="flex items-center space-x-4">
                <a href="" class="text-gray-400 hover:text-white transition text-sm">Plan du site</a>
                <span class="text-gray-600">|</span>
                <a href="" class="text-gray-400 hover:text-white transition text-sm">Professionnels</a>

                <a href="https://webinova.netlify.app/"
                    class="ml-4 inline-flex items-center bg-white/10 hover:bg-white/20 text-gray-200 px-3 py-1 rounded-full transition"
                    target="_blank" rel="noopener" title="Site créé par Webinova">
                    <i class="fas fa-rocket mr-2 text-gray-300"></i>
                    <span class="text-sm">Webinova</span>
                </a>
            </div>
        </div>

    </div>
</footer>
