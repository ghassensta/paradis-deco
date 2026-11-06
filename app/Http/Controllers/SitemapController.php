<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Configuration;
use App\Models\Inspiration;
use App\Models\Product;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Sitemap généré à la volée : remplace le fichier statique qui était figé
     * et ne contenait ni les nouvelles catégories ni les sous-catégories.
     */
    public function index()
    {
        $urls = [];

        foreach ([
            ['welcome', 1.0],
            ['allproduits', 0.8],
            ['allinspirations', 0.6],
            ['about', 0.5],
            ['contact', 0.5],
            ['faq', 0.5],
            ['mentions-legales', 0.3],
            ['politique-confidentialite', 0.3],
        ] as [$name, $priority]) {
            $urls[] = ['loc' => route($name), 'priority' => $priority];
        }

        Category::active()->roots()->with(['activeChildren' => fn ($q) => $q->ordered()])->ordered()
            ->get()
            ->each(function (Category $root) use (&$urls) {
                $urls[] = ['loc' => $root->url, 'lastmod' => $root->updated_at, 'priority' => 0.8];

                foreach ($root->activeChildren as $child) {
                    // Le parent est déjà chargé : on l'injecte pour éviter une
                    // requête par enfant lors du calcul de l'URL canonique.
                    $child->setRelation('parent', $root);

                    $urls[] = ['loc' => $child->url, 'lastmod' => $child->updated_at, 'priority' => 0.7];
                }
            });

        Product::active()->latest('updated_at')->get(['slug', 'updated_at'])
            ->each(function (Product $product) use (&$urls) {
                $urls[] = [
                    'loc' => route('preview-article', $product->slug),
                    'lastmod' => $product->updated_at,
                    'priority' => 0.7,
                ];
            });

        Inspiration::active()->get(['slug', 'updated_at'])
            ->each(function (Inspiration $inspiration) use (&$urls) {
                $urls[] = [
                    'loc' => route('preview-inspiration', $inspiration->slug),
                    'lastmod' => $inspiration->updated_at,
                    'priority' => 0.5,
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * llms.txt : plan du site en Markdown pour les moteurs de réponse IA.
     * Généré depuis le catalogue, comme le sitemap, pour ne jamais se périmer.
     */
    public function llms()
    {
        $link = fn (string $label, string $url, ?string $note = null) => "- [{$label}]({$url})" . ($note ? ": {$note}" : '');

        $categories = Category::active()->roots()->ordered()->get()
            ->map(fn (Category $category) => $link($category->name, $category->url, $category->description_courte));

        $products = Product::active()->orderBy('name')->get()
            ->map(fn (Product $product) => $link(
                $product->name,
                route('preview-article', $product->slug),
                Str::limit($product->short_description, 160)
            ));

        $lines = [
            '# ' . (Configuration::first()?->site_name ?: config('app.name')),
            '',
            '> Boutique de décoration d\'intérieur en ligne en Tunisie. Prix en dinars tunisiens (DT).',
            '',
            '## Pages',
            $link('Tous les produits', route('allproduits')),
            $link('Inspirations', route('allinspirations')),
            $link('FAQ', route('faq')),
            $link('À propos', route('about')),
            $link('Contact', route('contact')),
            '',
            '## Catégories',
            ...$categories,
            '',
            '## Produits',
            ...$products,
            '',
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /** robots.txt servi dynamiquement pour rester aligné avec les routes. */
    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /checkout',
            // Maquettes statiques héritées du gabarit : contenu dupliqué,
            // non liées depuis le site. À supprimer une fois confirmées inutiles.
            'Disallow: /cat$',
            'Disallow: /show$',
            'Disallow: /inspiration$',
            // Les pages paginées restent crawlables : elles portent un
            // noindex,follow qui laisse Google atteindre les fiches produits.
            '',
            'Sitemap: ' . route('sitemap'),
            '',
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
