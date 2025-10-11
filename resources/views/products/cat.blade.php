<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Paradis Déco | Catégories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            margin: 0;
        }

        /* Header Styles */
        .header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            height: 3rem;
        }

        .header nav a {
            color: #4b5563;
            font-weight: 500;
            margin-left: 1.5rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .header nav a:hover {
            color: #4f46e5;
        }

        /* Categories Section */
        .categories-section {
            padding: 4rem 1rem;
            background: white;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3rem;
            background: linear-gradient(to right, #4f46e5, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .category-btn {
            padding: 0.75rem 1.5rem;
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: 600;
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .category-btn:hover,
        .category-btn.active {
            background: #4f46e5;
            color: white;
            animation: none;
        }

        .category-content {
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .category-content.show {
            opacity: 1;
        }

        .category-name {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #4f46e5, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .category-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .category-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
            color: #1f2937;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .product-card .content {
            padding: 1.5rem;
        }

        .product-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-card p {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .product-card .price {
            color: #10b981;
            font-weight: bold;
            font-size: 1.25rem;
        }

        .product-card .rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #f59e0b;
            font-size: 0.875rem;
        }

        .add-to-cart {
            background: #4f46e5;
            color: white;
            padding: 0.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background: #4338ca;
        }

        .back-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #e5e7eb;
            color: #1f2937;
            font-weight: 600;
            border-radius: 9999px;
            text-align: center;
            margin: 2rem auto;
            transition: background 0.3s, color 0.3s;
        }

        .back-btn:hover {
            background: #4f46e5;
            color: white;
        }

        /* Animation pulse pour les boutons */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Animation fade-in */
        @keyframes fade-in {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fade-in 0.8s ease-in-out forwards;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .category-image {
                height: 300px;
            }

            .category-name,
            .category-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .category-btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <a href="/" class="flex items-center">
                <img src="{{ asset('assets/img/logo-image.jpg') }}" alt="Paradis Déco" />
            </a>
            <nav class="hidden md:flex">
                <a href="/produits">Nos produits</a>
                <a href="/inspirations">Inspirations</a>
                <a href="/services">Services</a>
                <a href="/blog">Blog déco</a>
            </nav>
            <div class="flex items-center gap-4">
                <button aria-label="Recherche" class="text-gray-600 hover:text-primary">
                    <i class="fas fa-search"></i>
                </button>
                <a href="/panier" class="text-gray-600 hover:text-primary relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</</span>
                </a>
            </div>
        </div>
    </header>

    <!-- CATÉGORIES -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Explorez Nos Catégories</h2>

            <!-- Sélecteur de catégories -->
            <div class="category-buttons">
                <button class="category-btn active" data-category="decoration">Décoration</button>
                <button class="category-btn" data-category="meubles">Meubles</button>
                <button class="category-btn" data-category="luminaires">Luminaires</button>
                <button class="category-btn" data-category="textiles">Textiles</button>
            </div>

            <!-- Contenu dynamique des catégories -->
            <div id="category-content" class="category-content show">
                <!-- Contenu chargé dynamiquement via JavaScript -->
            </div>

            <!-- Bouton de retour -->
            <button class="back-btn" id="back-btn">Retour aux catégories</button>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-100 py-16 border-t border-gray-200">
        <div class="container grid grid-cols-1 md:grid-cols-4 gap-10 text-sm text-gray-700">
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Paradis Déco</h4>
                <p class="text-xs text-gray-500">L’élégance contemporaine pour votre maison : accessoires, meubles, éclairages sélectionnés avec soin.</p>
                <div class="flex space-x-4 mt-4">
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="text-gray-600 hover:text-primary transition">
                        <i class="fab fa-facebook-f h-5 w-5"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram" class="text-gray-600 hover:text-primary transition">
                        <i class="fab fa-instagram h-5 w-5"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="text-gray-600 hover:text-primary transition">
                        <i class="fab fa-twitter h-5 w-5"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Collections</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-secondary transition">Packs</a></li>
                    <li><a href="#" class="hover:text-secondary transition">Nouveautés</a></li>
                    <li><a href="#" class="hover:text-secondary transition">Offres exclusives</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Informations</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-secondary transition">Contact</a></li>
                    <li><a href="#" class="hover:text-secondary transition">FAQ</a></li>
                    <li><a href="#" class="hover:text-secondary transition">Politique de retour</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wide mb-3">Newsletter</h4>
                <form class="flex flex-col space-y-3">
                    <input type="email" class="border border-gray-300 px-4 py-2 rounded-full text-sm" placeholder="Votre email" />
                    <button class="bg-secondary text-white px-6 py-2 rounded-full text-sm hover:bg-secondary-dark transition">S'inscrire</button>
                </form>
            </div>
        </div>
        <div class="text-center text-xs text-gray-400 mt-10">© {{ date('Y') }} Paradis Déco. Tous droits réservés.</div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryButtons = document.querySelectorAll('.category-btn');
            const categoryContent = document.getElementById('category-content');
            const backBtn = document.getElementById('back-btn');

            // Données des catégories et produits (peut être remplacé par une API)
            const categories = {
                decoration: {
                    image: 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1740&q=80',
                    title: 'Décoration',
                    products: [
                        { name: 'Bougie Parfumée', description: 'Eucalyptus & Menthe', price: '25 DT', image: 'https://imgs.search.brave.com/JJ72iD38GV95e3eg-Yz6howVFSBX5oYiKbOHtFRSSgc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/ZHVyYW5jZS5mci83/NzE4LWhvbWVfZGVm/YXVsdC9ib3VnaWUt/cGFyZnVtZWUtZXVj/YWx5cHR1cy1tZW50/aGUuanBn', rating: 4.8 },
                        { name: 'Vase Céramique', description: 'Style minimaliste', price: '45 DT', image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=500&q=80', rating: 4.6 }
                    ]
                },
                meubles: {
                    image: 'https://images.unsplash.com/photo-1600585152220-90363fe7e115?auto=format&fit=crop&w=1740&q=80',
                    title: 'Meubles',
                    products: [
                        { name: 'Table Basse', description: 'Bois massif', price: '150 DT', image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=500&q=80', rating: 4.7 },
                        { name: 'Fauteuil Scandinave', description: 'Tissu gris', price: '220 DT', image: 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=500&q=80', rating: 4.9 }
                    ]
                },
                luminaires: {
                    image: 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1740&q=80',
                    title: 'Luminaires',
                    products: [
                        { name: 'Lampe de Table', description: 'Design moderne', price: '85 DT', image: 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=500&q=80', rating: 4.5 },
                        { name: 'Suspension Industrielle', description: 'Métal noir', price: '120 DT', image: 'https://images.unsplash.com/photo-1513506003901-1e6a229e77cb?auto=format&fit=crop&w=500&q=80', rating: 4.8 }
                    ]
                },
                textiles: {
                    image: 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=1740&q=80',
                    title: 'Textiles',
                    products: [
                        { name: 'Coussin Brodé', description: 'Motif géométrique', price: '55 DT', image: 'https://imgs.search.brave.com/yrXGHOIGNLQ2DV6krRHOvc8kFRVy4OkGqM6q8L6UoGs/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL0kv/ODFIZlV2d1hWbkwu/anBn', rating: 4.5 },
                        { name: 'Plaid Laine', description: '100% laine naturelle', price: '95 DT', image: 'https://imgs.search.brave.com/1IR8-1L_aWlTnyH8bUoW1PGWVTC1vTHXap62N3tJ_UQ/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YXMubWFpc29uc2R1/bW9uZGUuY29tL2lt/YWdlcy9mX2F1dG8s/cV9hdXRvLHdfMzUw/L3YxL21rcC9NMjEw/MzA3MTZfMS9wbGFp/ZC1jYWNoZW1pcmUt/ZXQtbGFpbmUtY2hl/dnJvbnMtbWFycm9u/LWdsYWNlLTEzMC14/LTIzMC1jbS5qcGc', rating: 4.7 }
                    ]
                }
            };

            // Afficher le contenu par défaut (message initial)
            function displayDefaultContent() {
                categoryContent.innerHTML = `
                    <div class="text-center animate-fade-in">
                        <p class="text-gray-600 text-lg">Sélectionnez une catégorie pour découvrir nos produits</p>
                    </div>
                `;
                categoryContent.classList.add('show');
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                backBtn.style.display = 'none';
            }

            // Afficher le contenu d'une catégorie
            function displayCategory(category) {
                const data = categories[category];
                categoryContent.innerHTML = `
                    <div class="category-name animate-fade-in">${data.title}</div>
                    <img src="${data.image}" alt="${data.title}" class="category-image animate-fade-in">
                    <h2 class="category-title">${data.title}</h2>
                    <div class="product-grid">
                        ${data.products.map(product => `
                            <div class="product-card">
                                <img src="${product.image}" alt="${product.name}">
                                <div class="content">
                                    <h3>${product.name}</h3>
                                    <p>${product.description}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="price">${product.price}</span>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span>${product.rating}</span>
                                        </div>
                                    </div>
                                    <button class="add-to-cart" aria-label="Ajouter au panier">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
                categoryContent.classList.add('show');
                backBtn.style.display = 'block';
            }

            // Gestion des clics sur les boutons de catégorie
            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    categoryContent.classList.remove('show');
                    setTimeout(() => displayCategory(button.dataset.category), 500);
                });
            });

            // Gestion du bouton de retour
            backBtn.addEventListener('click', () => {
                categoryContent.classList.remove('show');
                setTimeout(displayDefaultContent, 500);
            });

            // Afficher le contenu par défaut au chargement
            displayDefaultContent();
        });
    </script>
</body>
</html>
