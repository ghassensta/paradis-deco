<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\Statut;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\User;
use App\Models\Boutique;
use App\Models\ModePaiment;
use App\Models\Pak;
use App\Models\Commande;
use App\Models\Companie;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Création des rôles
        $roles = ['superadmin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2) Définition des utilisateurs à créer
        $users = [
            [
                'name' => 'mohamedfatnassi',
                'email' => 'mohamedfatnassi@paradis-deco.tn',
                'password' => Hash::make('20202020'),
                'role' => 'superadmin',
            ],
            // ... ajoutez d’autres utilisateurs ici si besoin
        ];

        // 3) Création/mise à jour et assignation de rôle
        foreach ($users as $userData) {
            // Récupérer le rôle
            $roleName = $userData['role'];

            // On retire la clé 'role' pour ne pas la passer à fill()
            unset($userData['role']);

            // Création ou mise à jour
            $user = User::firstOrCreate(
                ['email' => $userData['email']],  // condition de recherche
                $userData                         // valeurs à insérer si absent
            );

            // On met à jour au cas où le password ou le statut aurait changé
            $user->fill($userData)->save();

            // Assignation du rôle (Spatie)
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }


        $statuts = [
            ['name' => 'Annulé', 'is_publish' => true],
            ['name' => 'Livré et payé', 'is_publish' => true],
            ['name' => 'Livrée et payée', 'is_publish' => true],
            ['name' => 'En cours de traitement', 'is_publish' => false],
            ['name' => 'En cours de livraison', 'is_publish' => false],
        ];
        foreach ($statuts as $statut) {
            Statut::firstOrCreate(['name' => $statut['name']], $statut);
        }


        $products = [
            // 1
            [
                'name' => 'Bougie Parfumée Eucalyptus & Menthe',
                'slug' => 'bougie-eucalyptus-menthe',
                'description' => 'Bougie artisanale aux notes fraîches d\'eucalyptus et de menthe – 50h de combustion.',
                'price' => 25.00,
                'stock' => 100,
                'sku' => 'BOUGIE001',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Bougie Parfumée Eucalyptus & Menthe | Paradis Déco',
                'meta_description' => 'Découvrez notre bougie parfumée artisanale eucalyptus & menthe, 50h de combustion, fabriquée en Tunisie.',
                'meta_keywords' => 'bougie, eucalyptus, menthe, artisanale, déco',
                'og_image' => null,
            ],
            // 2
            [
                'name' => 'Coussin Brodé Géométrique',
                'slug' => 'coussin-brode-geometrique',
                'description' => 'Coussin décoratif brodé motif géométrique en coton premium.',
                'price' => 55.00,
                'stock' => 50,
                'sku' => 'COUSSIN002',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Coussin Brodé Géométrique | Paradis Déco',
                'meta_description' => 'Coussin brodé motif géométrique en coton, parfait pour apporter une touche d’originalité à votre salon.',
                'meta_keywords' => 'coussin, brodé, géométrique, déco, salon',
                'og_image' => null,
            ],
            // 3
            [
                'name' => 'Horloge Murale Bois Scandinave',
                'slug' => 'horloge-murale-bois-scandinave',
                'description' => 'Horloge murale en bois au design scandinave, silencieuse et élégante.',
                'price' => 75.00,
                'stock' => 30,
                'sku' => 'HORLOGE003',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Horloge Murale Bois Scandinave | Paradis Déco',
                'meta_description' => 'Donnez du cachet à votre intérieur avec notre horloge murale en bois, design scandinave et mécanisme silencieux.',
                'meta_keywords' => 'horloge, bois, scandinave, murale, silencieuse',
                'og_image' => null,
            ],
            // 4
            [
                'name' => 'Plaid Laine Naturelle',
                'slug' => 'plaid-laine-naturelle',
                'description' => 'Plaid en laine 100% naturelle, doux et réchauffant, tissé à la main.',
                'price' => 95.00,
                'stock' => 20,
                'sku' => 'PLAID004',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Plaid Laine Naturelle | Paradis Déco',
                'meta_description' => 'Plaid cosy en laine naturelle, conçu pour vous apporter chaleur et confort tout en sublimant votre canapé.',
                'meta_keywords' => 'plaid, laine, naturelle, déco, cosy',
                'og_image' => null,
            ],
            // 5
            [
                'name' => 'Diffuseur Bambou & Verre',
                'slug' => 'diffuseur-bambou-verre',
                'description' => 'Diffuseur d\'huiles essentielles en bambou et verre, design épuré.',
                'price' => 45.00,
                'stock' => 40,
                'sku' => 'DIFF005',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Diffuseur Bambou & Verre | Paradis Déco',
                'meta_description' => 'Diffuseur design en bambou et verre pour parfumer délicatement votre intérieur avec vos huiles essentielles.',
                'meta_keywords' => 'diffuseur, bambou, verre, huiles essentielles, aromathérapie',
                'og_image' => null,
            ],
            // 6
            [
                'name' => 'Vase Minimaliste Céramique',
                'slug' => 'vase-minimaliste-ceramique',
                'description' => 'Vase en céramique blanche, silhouette minimaliste, parfait pour bouquets épurés.',
                'price' => 45.00,
                'stock' => 60,
                'sku' => 'VASE006',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Vase Minimaliste Céramique | Paradis Déco',
                'meta_description' => 'Apportez une touche de minimalisme à votre maison avec ce vase en céramique blanche au design pur.',
                'meta_keywords' => 'vase, minimaliste, céramique, déco, maison',
                'og_image' => null,
            ],
            // 7
            [
                'name' => 'Cadre Photo Moderne',
                'slug' => 'cadre-photo-moderne',
                'description' => 'Cadre photo au style moderne, bords fins, disponible en plusieurs tailles.',
                'price' => 35.00,
                'stock' => 80,
                'sku' => 'CADRE007',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Cadre Photo Moderne | Paradis Déco',
                'meta_description' => 'Exposez vos plus beaux souvenirs dans notre cadre photo au design contemporain et fin.',
                'meta_keywords' => 'cadre photo, moderne, décoration murale, souvenir',
                'og_image' => null,
            ],
            // 8
            [
                'name' => 'Lampe Artisanale en Rotin',
                'slug' => 'lampe-artisanale-rotin',
                'description' => 'Lampe de table artisanale en rotin naturel, ambiance chaleureuse garantie.',
                'price' => 89.00,
                'stock' => 25,
                'sku' => 'LAMPE008',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Lampe Artisanale en Rotin | Paradis Déco',
                'meta_description' => 'Apportez une lumière douce et naturelle avec cette lampe artisanale en rotin tressé.',
                'meta_keywords' => 'lampe, rotin, artisanal, ambiance, éclairage',
                'og_image' => null,
            ],
            // 9
            [
                'name' => 'Tapis Kilim Vintage',
                'slug' => 'tapis-kilim-vintage',
                'description' => 'Tapis kilim vintage, motifs traditionnels, coloris chauds.',
                'price' => 120.00,
                'stock' => 15,
                'sku' => 'TAPIS009',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Tapis Kilim Vintage | Paradis Déco',
                'meta_description' => 'Sublimez votre sol avec notre tapis kilim vintage, riche en couleurs et en histoire.',
                'meta_keywords' => 'tapis, kilim, vintage, sol, décoration',
                'og_image' => null,
            ],
            // 10
            [
                'name' => 'Set 2 Bougies Lavande',
                'slug' => 'set-2-bougies-lavande',
                'description' => 'Lot de 2 bougies parfumées à la lavande – 40h de combustion chacune.',
                'price' => 45.00,
                'stock' => 70,
                'sku' => 'BOUGIE010',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Set 2 Bougies Lavande | Paradis Déco',
                'meta_description' => 'Profitez du parfum apaisant de la lavande avec ce lot de 2 bougies artisanales – 40h de diffusion.',
                'meta_keywords' => 'bougie, lavande, lot, relaxation, déco',
                'og_image' => null,
            ],
            // 11
            [
                'name' => 'Diffuseur Céramique & Bois',
                'slug' => 'diffuseur-ceramique-bois',
                'description' => 'Diffuseur ultrasonique en céramique et bois clair, 100 ml.',
                'price' => 60.00,
                'stock' => 35,
                'sku' => 'DIFF011',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Diffuseur Céramique & Bois | Paradis Déco',
                'meta_description' => 'Diffuseur ultrasonique en céramique et bois pour un intérieur parfumé et design.',
                'meta_keywords' => 'diffuseur, céramique, bois, ultrasonique, déco',
                'og_image' => null,
            ],
            // 12
            [
                'name' => 'Miroir Rond Murale',
                'slug' => 'miroir-rond-murale',
                'description' => 'Miroir mural rond avec cadre doré, diamètre 60 cm.',
                'price' => 85.00,
                'stock' => 22,
                'sku' => 'MIROIR012',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Miroir Rond Murale | Paradis Déco',
                'meta_description' => 'Égayez votre pièce avec ce miroir mural rond au cadre doré – chic et contemporain.',
                'meta_keywords' => 'miroir, rond, murale, doré, design',
                'og_image' => null,
            ],
            // 13
            [
                'name' => 'Potager Intérieur Hydroponique',
                'slug' => 'potager-interieur-hydroponique',
                'description' => 'Mini potager d’intérieur en hydroponie, éclairage LED inclus.',
                'price' => 150.00,
                'stock' => 10,
                'sku' => 'JARDIN013',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Potager Intérieur Hydroponique | Paradis Déco',
                'meta_description' => 'Cultivez vos herbes aromatiques en intérieur grâce à ce potager hydroponique compact avec LED.',
                'meta_keywords' => 'potager, hydroponique, intérieur, LED, jardin',
                'og_image' => null,
            ],
            // 14
            [
                'name' => 'Table Basse Industrielle',
                'slug' => 'table-basse-industrielle',
                'description' => 'Table basse en métal noir et plateau bois recyclé, style industriel.',
                'price' => 220.00,
                'stock' => 12,
                'sku' => 'TABLE014',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Table Basse Industrielle | Paradis Déco',
                'meta_description' => 'Apportez une touche loft à votre salon avec cette table basse style industriel en métal et bois recyclé.',
                'meta_keywords' => 'table basse, industriel, métal, bois recyclé, salon',
                'og_image' => null,
            ],
            // 15
            [
                'name' => 'Chaise Scandinave Assise Cuir',
                'slug' => 'chaise-scandinave-assise-cuir',
                'description' => 'Chaise au design scandinave avec assise en cuir véritable et pieds en hêtre.',
                'price' => 135.00,
                'stock' => 18,
                'sku' => 'CHAISE015',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Chaise Scandinave Assise Cuir | Paradis Déco',
                'meta_description' => 'Alliez confort et élégance avec cette chaise scandinave à assise cuir et pieds en hêtre.',
                'meta_keywords' => 'chaise, scandinave, cuir, hêtre, design',
                'og_image' => null,
            ],
            // 16
            [
                'name' => 'Bibliothèque Étagère Murale',
                'slug' => 'bibliotheque-etagere-murale',
                'description' => 'Étagère murale modulable en métal et bois, parfaite pour livres et déco.',
                'price' => 180.00,
                'stock' => 14,
                'sku' => 'BIBLI016',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Bibliothèque Étagère Murale | Paradis Déco',
                'meta_description' => 'Organisez vos livres et objets déco sur cette bibliothèque murale design, modulable et robuste.',
                'meta_keywords' => 'bibliothèque, étagère, murale, modulable, déco',
                'og_image' => null,
            ],
            // 17
            [
                'name' => 'Set 3 Plats Céramique',
                'slug' => 'set-3-plats-ceramique',
                'description' => 'Assortiment de 3 plats en céramique artisanale, idéal pour présentation.',
                'price' => 65.00,
                'stock' => 45,
                'sku' => 'PLAT017',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Set 3 Plats Céramique | Paradis Déco',
                'meta_description' => 'Présentez vos mets avec élégance grâce à ce set de 3 plats en céramique artisanale.',
                'meta_keywords' => 'plats, céramique, assortiment, table, déco',
                'og_image' => null,
            ],
            // 18
            [
                'name' => 'Horloge de Table Vintage',
                'slug' => 'horloge-de-table-vintage',
                'description' => 'Horloge de table style vintage, finition métal vieilli.',
                'price' => 55.00,
                'stock' => 27,
                'sku' => 'HORLOGE018',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Horloge de Table Vintage | Paradis Déco',
                'meta_description' => 'Apportez du caractère à votre bureau avec cette horloge de table vintage en métal vieilli.',
                'meta_keywords' => 'horloge, table, vintage, métal, bureau',
                'og_image' => null,
            ],
            // 19
            [
                'name' => 'Pot Fleur en Terre Cuite',
                'slug' => 'pot-fleur-terre-cuite',
                'description' => 'Pot à fleurs en terre cuite, émaillé blanc, diamètre 20 cm.',
                'price' => 30.00,
                'stock' => 90,
                'sku' => 'POT019',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Pot Fleur en Terre Cuite | Paradis Déco',
                'meta_description' => 'Mettez en valeur vos plantes avec ce pot en terre cuite émaillé blanc, simple et élégant.',
                'meta_keywords' => 'pot, fleurs, terre cuite, jardin, déco',
                'og_image' => null,
            ],
            // 20
            [
                'name' => 'Diffuseur Ultrasonique d’Ambiance',
                'slug' => 'diffuseur-ultrasonique-ambiance',
                'description' => 'Diffuseur ultrasonique en verre dépoli, 200 ml, éclairage LED multicolore.',
                'price' => 80.00,
                'stock' => 33,
                'sku' => 'DIFF020',
                'is_active' => true,
                'images' => [],
                'meta_title' => 'Diffuseur Ultrasonique d’Ambiance | Paradis Déco',
                'meta_description' => 'Diffuseur ultrasonique en verre dépoli pour créer une atmosphère zen grâce à son éclairage LED.',
                'meta_keywords' => 'diffuseur, ultrasonique, ambiance, LED, zen',
                'og_image' => null,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $clients = [
            [
                'name' => 'Amina Ben Salah',
                'email' => 'amina.bensalah@example.com',
                'phone' => '+21620123456',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Mohamed Trabelsi',
                'email' => 'mohamed.trabelsi@example.com',
                'phone' => '+21621456789',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Leila Hammami',
                'email' => 'leila.hammami@example.com',
                'phone' => '+21622987654',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Ahmed Zarrouk',
                'email' => 'ahmed.zarrouk@example.com',
                'phone' => '+21620876543',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Sonia Chebil',
                'email' => 'sonia.chebil@example.com',
                'phone' => '+21620765432',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Walid Brahim',
                'email' => 'walid.brahim@example.com',
                'phone' => '+21621654321',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Farah Jaziri',
                'email' => 'farah.jaziri@example.com',
                'phone' => '+21620543210',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Tarek Ben Abdallah',
                'email' => 'tarek.abdallah@example.com',
                'phone' => '+21620321098',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Yasmine Fakhfakh',
                'email' => 'yasmine.fakhfakh@example.com',
                'phone' => '+21620210987',
                'adresse'=>'sousse'
            ],
            [
                'name' => 'Hatem Khlifi',
                'email' => 'hatem.khlifi@example.com',
                'phone' => '+21620109876',
                'adresse'=>'sousse'
            ],
        ];

        foreach ($clients as $data) {
            Client::firstOrCreate(['email' => $data['email']], $data);
        }


        /* ---------------------------------------------------------------
         | Catégories et sous-catégories (hiérarchie à deux niveaux)
         --------------------------------------------------------------- */
        $tree = [
            'Senteurs & Ambiance' => [
                'description' => 'Bougies, diffuseurs et parfums d\'intérieur pour créer une atmosphère chaleureuse.',
                'children' => ['Bougies parfumées', 'Diffuseurs & parfums d\'intérieur'],
            ],
            'Décoration murale' => [
                'description' => 'Horloges, miroirs et cadres pour habiller vos murs avec caractère.',
                'children' => ['Horloges murales', 'Miroirs & cadres'],
            ],
            'Textile & Confort' => [
                'description' => 'Coussins, plaids et tapis pour une intérieur douillet et accueillant.',
                'children' => ['Coussins & plaids', 'Tapis'],
            ],
            'Mobilier & Rangement' => [
                'description' => 'Tables, assises et étagères au design épuré.',
                'children' => ['Tables & assises', 'Étagères & rangement'],
            ],
            'Art de la table & Vases' => [
                'description' => 'Vases, pots et vaisselle pour sublimer vos réceptions.',
                'children' => ['Vases & pots', 'Vaisselle'],
            ],
            // Volontairement sans sous-catégorie : le front doit gérer ce cas.
            'Luminaires' => [
                'description' => 'Lampes et éclairages d\'ambiance pour chaque pièce.',
                'children' => [],
            ],
        ];

        $order = 0;
        $bySlug = [];

        foreach ($tree as $name => $node) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $node['description'],
                    'is_active' => true,
                    'sort_order' => $order += 10,
                ]
            );
            $bySlug[$parent->slug] = $parent;

            $childOrder = 0;
            foreach ($node['children'] as $childName) {
                $child = Category::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'is_active' => true,
                        'sort_order' => $childOrder += 10,
                    ]
                );
                $bySlug[$child->slug] = $child;
            }
        }

        // Rattachement des produits à leur sous-catégorie (par slug de produit).
        $assignments = [
            'bougies-parfumees' => ['bougie-eucalyptus-menthe', 'set-2-bougies-lavande'],
            'diffuseurs-parfums-dinterieur' => ['diffuseur-bambou-verre', 'diffuseur-ceramique-bois', 'diffuseur-ultrasonique-ambiance'],
            'horloges-murales' => ['horloge-murale-bois-scandinave', 'horloge-de-table-vintage'],
            'miroirs-cadres' => ['miroir-rond-murale', 'cadre-photo-moderne'],
            'coussins-plaids' => ['coussin-brode-geometrique', 'plaid-laine-naturelle'],
            'tapis' => ['tapis-kilim-vintage'],
            'tables-assises' => ['table-basse-industrielle', 'chaise-scandinave-assise-cuir'],
            'etageres-rangement' => ['bibliotheque-etagere-murale'],
            'vases-pots' => ['vase-minimaliste-ceramique', 'pot-fleur-terre-cuite', 'potager-interieur-hydroponique'],
            'vaisselle' => ['set-3-plats-ceramique'],
            'luminaires' => ['lampe-artisanale-rotin'],
        ];

        foreach ($assignments as $categorySlug => $productSlugs) {
            $category = $bySlug[$categorySlug] ?? null;

            if (! $category) {
                continue;
            }

            foreach ($productSlugs as $productSlug) {
                $product = Product::where('slug', $productSlug)->first();

                if (! $product) {
                    continue;
                }

                $ids = collect($product->category_ids)
                    ->push($category->id)
                    ->when($category->parent_id, fn ($c) => $c->push($category->parent_id))
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                $product->update(['category_ids' => $ids]);
            }
        }

        $settings = [
            [
                'site_name' => 'Paradis-deco',
                'site_logo' => 'logo.png',
                'support_email' => 'support@myawesomesite.com',
                'default_language' => 'fr',
                'currency' => 'TND',
                'meta_title' => 'Welcome to My Awesome Site',
                'meta_description' => 'This is an awesome site built with Laravel.',
                'shipping_cost' => 5.99,
                'free_shipping_threshold' => 50.00,
                'delivery_estimate_days' => 3,
                'maintenance_mode' => false,
                'homepage_banner' => 'banner.jpg',
            ],
        ];

        foreach ($settings as $setting) {
            Configuration::firstOrCreate([], $setting);
        }
    }
    }


