<header class="bg-white shadow-md sticky top-0 z-40">
    <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="inline-block">
            <img src="{{ $config->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/img/cover-image-removebg-preview.png') }}"
                 width="80" height="100" alt="Logo-{{ $config->site_name }}">
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex space-x-8">
            <a href="/" class="text-gray-600 hover:text-[#dfb54e] transition">Accueil</a>
            <a href="{{ route('allproduits') }}" class="text-gray-600 hover:text-[#dfb54e] transition">Produits</a>
            <a href="{{ route('about') }}" class="text-gray-600 hover:text-[#dfb54e] transition">À propos</a>
            <a href="{{ route('contact') }}" class="text-gray-600 hover:text-[#dfb54e] transition">Contact</a>
        </div>

        <!-- Icons -->
        <div class="flex items-center space-x-6">
            <!-- Cart -->
            <div class="relative">
                <button aria-label="Panier" class="cart-button flex items-center text-gray-600 hover:text-[#dfb54e] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cartCount" class="ml-1 text-sm font-semibold">0</span>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuButton" class="md:hidden text-gray-600 hover:text-[#dfb54e] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-30 z-50 backdrop-blur-sm hidden transition-opacity duration-300">
        <div class="absolute right-0 top-0 h-full w-80 bg-white shadow-xl transform transition-transform duration-300 ease-in-out translate-x-full">
            <div class="flex justify-between items-center p-6 border-b">
                <span class="text-xl font-bold">Menu</span>
                <button id="closeMobileMenu" class="text-gray-600 hover:text-[#dfb54e] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex flex-col p-6 space-y-6">
                <a href="/" class="text-lg py-2 text-gray-600 hover:text-[#dfb54e] transition">Accueil</a>
                <a href="{{ route('allproduits') }}" class="text-lg py-2 text-gray-600 hover:text-[#dfb54e] transition">Produits</a>
                <a href="{{ route('about') }}" class="text-lg py-2 text-gray-600 hover:text-[#dfb54e] transition">À propos</a>
                <a href="{{ route('contact') }}" class="text-lg py-2 text-gray-600 hover:text-[#dfb54e] transition">Contact</a>
            </nav>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuPanel = mobileMenuOverlay.querySelector('div');

    // Ouvrir le menu
    mobileMenuButton.addEventListener('click', function() {
        mobileMenuOverlay.classList.remove('hidden');
        setTimeout(() => {
            mobileMenuOverlay.classList.remove('opacity-0');
            mobileMenuPanel.classList.remove('translate-x-full');
        }, 10);
    });

    // Fermer le menu
    function closeMenu() {
        mobileMenuPanel.classList.add('translate-x-full');
        mobileMenuOverlay.classList.add('opacity-0');
        setTimeout(() => {
            mobileMenuOverlay.classList.add('hidden');
        }, 300);
    }

    closeMobileMenu.addEventListener('click', closeMenu);

    // Fermer en cliquant sur l'overlay
    mobileMenuOverlay.addEventListener('click', function(e) {
        if (e.target === mobileMenuOverlay) {
            closeMenu();
        }
    });

    // Fermer avec la touche ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !mobileMenuOverlay.classList.contains('hidden')) {
            closeMenu();
        }
    });
});
</script>
<style>
    /* Ajoutez ceci si vous n'utilisez pas Tailwind */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

.transition-opacity {
    transition-property: opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
