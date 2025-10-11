<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Paradis Déco | Inspirations</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
  />
  <style>
      .section-title {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5rem;
            color: #1f2937;
            position: relative;
        }

        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #4f46e5, #10b981);
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
        }
  </style>
  @vite('resources/css/app.css')
</head>
<body class="font-inter bg-gray-100 text-gray-800">

  <!-- HEADER -->
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-6 md:px-12 flex items-center justify-between h-16">
      <a href="/" class="flex items-center">
        <img
          src="{{ asset('assets/img/logo-image.jpg') }}"
          alt="Paradis Déco"
          class="h-12"
        />
      </a>
      <nav class="hidden md:flex space-x-8">
        <a href="/produits" class="text-gray-600 font-medium hover:text-primary transition"
          >Nos produits</a
        >
        <a
          href="/inspirations"
          class="text-gray-600 font-medium hover:text-primary transition"
          >Inspirations</a
        >
        <a href="/services" class="text-gray-600 font-medium hover:text-primary transition"
          >Services</a
        >
        <a href="/blog" class="text-gray-600 font-medium hover:text-primary transition"
          >Blog déco</a
        >
      </nav>
      <div class="flex items-center space-x-6">
        <button
          aria-label="Recherche"
          class="text-gray-600 hover:text-primary transition text-xl"
        >
          <i class="fas fa-search"></i>
        </button>
        <a
          href="/panier"
          class="relative text-gray-600 hover:text-primary transition text-xl"
        >
          <i class="fas fa-shopping-cart"></i>
          <span
            class="absolute -top-2 -right-2 bg-primary text-white text-xs font-semibold rounded-full h-5 w-5 flex items-center justify-center"
            >3</span
          >
        </a>
      </div>
    </div>
  </header>

  <!-- INSPIRATIONS (nouveau design vertical) -->
  <section class="py-16">
    <div class="container mx-auto px-6 md:px-12 space-y-24">

                    <h2 class="section-title">Découvrez Nos Inspirations Déco</h2>

      <!-- Inspiration 1 : Salon Moderne -->
      <div class="space-y-6">
        <!-- Titre en dégradé -->
        <h2
          class="text-4xl md:text-5xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary"
        >
          Salon Moderne
        </h2>
        <!-- Image pleine largeur, centrée, avec ombre -->
        <div class="w-full max-w-4xl mx-auto">
          <img
            src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1740&q=80"
            alt="Salon Moderne"
            class="w-full h-auto rounded-lg shadow-lg"
          />
        </div>
        <!-- Description centrée -->
        <p class="text-center text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">
          Une ambiance épurée avec des lignes nettes, des couleurs neutres et des touches
          de sophistication pour un salon élégant et accueillant. Inspirez-vous de ce style
          pour créer un espace à la fois chaleureux et contemporain au cœur de votre maison.
        </p>
      </div>

      <!-- Inspiration 2 : Chambre Bohème -->
      <div class="space-y-6">
        <h2
          class="text-4xl md:text-5xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary"
        >
          Chambre Bohème
        </h2>
        <div class="w-full max-w-4xl mx-auto">
          <img
            src="https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=1740&q=80"
            alt="Chambre Bohème"
            class="w-full h-auto rounded-lg shadow-lg"
          />
        </div>
        <p class="text-center text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">
          Un style chaleureux et éclectique, mêlant textiles doux, motifs naturels et touches
          artisanales pour une chambre cosy. Les couleurs chaudes et les matières naturelles
          créent une atmosphère apaisante et inspirante à la fois.
        </p>
      </div>

      <!-- Inspiration 3 : Cuisine Fonctionnelle -->
      <div class="space-y-6">
        <h2
          class="text-4xl md:text-5xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary"
        >
          Cuisine Fonctionnelle
        </h2>
        <div class="w-full max-w-4xl mx-auto">
          <img
            src="https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?auto=format&fit=crop&w=1740&q=80"
            alt="Cuisine Fonctionnelle"
            class="w-full h-auto rounded-lg shadow-lg"
          />
        </div>
        <p class="text-center text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">
          Optimisez votre espace avec une cuisine moderne, pratique et esthétique, parfaite pour
          les amateurs de design fonctionnel. Réservez chaque superficie de travail pour un
          rangement astucieux et un confort maximal au quotidien.
        </p>
      </div>

      <!-- Inspiration 4 : Bureau Minimaliste -->
      <div class="space-y-6">
        <h2
          class="text-4xl md:text-5xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary"
        >
          Bureau Minimaliste
        </h2>
        <div class="w-full max-w-4xl mx-auto">
          <img
            src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1740&q=80"
            alt="Bureau Minimaliste"
            class="w-full h-auto rounded-lg shadow-lg"
          />
        </div>
        <p class="text-center text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">
          Un espace de travail épuré et organisé, conçu pour favoriser la productivité et la sérénité.
          Choisissez des lignes nettes, du mobilier épuré et une palette neutre pour un bureau
          sans distractions.
        </p>
      </div>

    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-gray-50 py-16 border-t border-gray-200">
    <div class="container mx-auto px-6 md:px-12 grid grid-cols-1 md:grid-cols-4 gap-10 text-sm text-gray-700">
      <div>
        <h4 class="font-bold uppercase tracking-wide mb-3 text-gray-900">Paradis Déco</h4>
        <p class="text-xs text-gray-500">
          L’élégance contemporaine pour votre maison : accessoires, meubles, éclairages sélectionnés
          avec soin.
        </p>
        <div class="flex space-x-4 mt-4">
          <a
            href="https://facebook.com"
            target="_blank"
            aria-label="Facebook"
            class="text-gray-600 hover:text-primary transition text-lg"
          >
            <i class="fab fa-facebook-f"></i>
          </a>
          <a
            href="https://instagram.com"
            target="_blank"
            aria-label="Instagram"
            class="text-gray-600 hover:text-primary transition text-lg"
          >
            <i class="fab fa-instagram"></i>
          </a>
          <a
            href="https://twitter.com"
            target="_blank"
            aria-label="Twitter"
            class="text-gray-600 hover:text-primary transition text-lg"
          >
            <i class="fab fa-twitter"></i>
          </a>
        </div>
      </div>
      <div>
        <h4 class="font-bold uppercase tracking-wide mb-3 text-gray-900">Collections</h4>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-secondary transition">Packs</a></li>
          <li><a href="#" class="hover:text-secondary transition">Nouveautés</a></li>
          <li><a href="#" class="hover:text-secondary transition">Offres exclusives</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold uppercase tracking-wide mb-3 text-gray-900">Informations</h4>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-secondary transition">Contact</a></li>
          <li><a href="#" class="hover:text-secondary transition">FAQ</a></li>
          <li><a href="#" class="hover:text-secondary transition">Politique de retour</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold uppercase tracking-wide mb-3 text-gray-900">Newsletter</h4>
        <form class="flex flex-col space-y-3">
          <input
            type="email"
            class="border border-gray-300 px-4 py-2 rounded-full text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Votre email"
          />
          <button
            class="bg-secondary text-white px-6 py-2 rounded-full text-sm hover:bg-secondary-dark transition focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-opacity-50"
          >
            S'inscrire
          </button>
        </form>
      </div>
    </div>
    <div class="text-center text-xs text-gray-400 mt-10">
      © {{ date('Y') }} Paradis Déco. Tous droits réservés.
    </div>
  </footer>

</body>
</html>
