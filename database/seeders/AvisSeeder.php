<?php

namespace Database\Seeders;

use App\Models\Avis;
use App\Models\Product;
use App\Support\RichText;
use Illuminate\Database\Seeder;

/**
 * Cinq avis de démonstration, rattachés à des produits réellement présents
 * en base. Aucun produit n'est créé ici : si un slug est absent, l'avis
 * correspondant est simplement ignoré et signalé.
 *
 * Idempotence : la clé stable est le couple (product_id, name), les deux
 * seules colonnes qui identifient un avis sans ambiguïté dans le schéma actuel.
 */
class AvisSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->avis() as $avis) {
            $product = Product::where('slug', $avis['product_slug'])->first();

            if (! $product) {
                $this->command?->warn("AvisSeeder : produit « {$avis['product_slug']} » introuvable, avis ignoré.");
                continue;
            }

            Avis::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $avis['name'],
                ],
                [
                    // Un avis est du texte : même traitement que le formulaire public.
                    'comment' => RichText::toText($avis['comment']),
                    'location' => $avis['location'],
                    'rating' => $avis['rating'],
                    'approved' => true,
                ]
            );
        }
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function avis(): array
    {
        return [
            [
                'product_slug' => 'plaid-laine-naturelle',
                'name' => 'Sonia M.',
                'location' => 'Tunis',
                'rating' => 5,
                'comment' => "La laine est vraiment douce, rien à voir avec les plaids synthétiques que j'avais avant. La couleur est fidèle aux photos, un peu plus claire à la lumière du jour. Livré en trois jours.",
            ],
            [
                'product_slug' => 'tapis-kilim-vintage',
                'name' => 'Amine B.',
                'location' => 'La Marsa',
                'rating' => 5,
                'comment' => "Très belle finition, les motifs sont nets et les couleurs bien tenues. Il a complètement changé l'allure du salon. L'emballage était soigné, le tapis est arrivé roulé et protégé.",
            ],
            [
                'product_slug' => 'lampe-artisanale-rotin',
                'name' => 'Mariem K.',
                'location' => 'Sousse',
                'rating' => 4,
                'comment' => "J'aime beaucoup la lumière qu'elle diffuse le soir, le tressage fait un joli motif sur le mur. Seul bémol : le câble est un peu court, j'ai dû déplacer la lampe pour atteindre la prise.",
            ],
            [
                'product_slug' => 'bougie-eucalyptus-menthe',
                'name' => 'Yassine H.',
                'location' => 'Sfax',
                'rating' => 5,
                'comment' => "Le parfum est frais sans être entêtant, il tient bien dans une pièce moyenne. Commande passée un dimanche soir et confirmée le lendemain matin, c'était simple.",
            ],
            [
                'product_slug' => 'table-basse-industrielle',
                'name' => 'Nour S.',
                'location' => 'Ariana',
                'rating' => 4,
                'comment' => "Bon rapport qualité prix pour ce format. Le plateau est solide et le piétement stable une fois monté. Le montage prend une vingtaine de minutes, la notice aurait pu être plus claire.",
            ],
        ];
    }
}
