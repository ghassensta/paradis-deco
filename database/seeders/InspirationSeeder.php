<?php

namespace Database\Seeders;

use App\Models\Inspiration;
use App\Support\RichText;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Cinq inspirations de démonstration, rédigées à partir du catalogue réel
 * (catégories et produits existants uniquement).
 *
 * Le seeder est idempotent : la clé stable est le slug. Les visuels ne sont
 * jamais fabriqués ni téléchargés — ils pointent vers des fichiers déjà
 * présents sur le disque « public ». Si une image a été remplacée depuis le
 * back-office, le seeder ne l'écrase pas.
 */
class InspirationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->inspirations() as $inspiration) {
            $slug = $inspiration['slug'];

            $attributes = [
                'title' => $inspiration['title'],
                // Même traitement que le back-office : le contenu passe par le
                // sanitizer avant d'atteindre la base.
                'resume' => RichText::toText($inspiration['resume']) ?: null,
                'description' => RichText::toHtml($inspiration['description']) ?: null,
                'meta_title' => $inspiration['meta_title'],
                'meta_description' => $inspiration['meta_description'],
                'is_active' => true,
            ];

            $existing = Inspiration::where('slug', $slug)->first();

            // L'image n'est posée qu'à la création, ou si celle enregistrée a
            // disparu du disque : un visuel choisi en back-office est conservé.
            if ($image = $this->resolveImage($existing, $inspiration['image'])) {
                $attributes['image'] = $image;
            }

            Inspiration::updateOrCreate(['slug' => $slug], $attributes);
        }
    }

    /** Retourne le chemin à écrire, ou null s'il faut laisser la valeur en place. */
    private function resolveImage(?Inspiration $existing, string $candidate): ?string
    {
        $disk = Storage::disk('public');

        if ($existing && $existing->image && $disk->exists($existing->image)) {
            return null;
        }

        return $disk->exists($candidate) ? $candidate : null;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function inspirations(): array
    {
        return [
            [
                'slug' => 'inspiration-chambre-elegante-chaleureuse',
                'title' => 'Comment créer une chambre élégante et chaleureuse',
                'meta_title' => 'Chambre élégante et chaleureuse : 6 conseils déco',
                'meta_description' => "Couleurs, textiles, lumière tamisée et senteurs : nos conseils pour transformer une chambre ordinaire en cocon élégant et chaleureux.",
                'image' => 'categories/2b1023bc-37d1-4c42-a99a-aaebc76f1961.webp',
                'resume' => "La chambre est la pièce où l'on passe le plus de temps, et souvent celle que l'on décore en dernier. Quelques choix simples — une palette calme, des textiles superposés et un éclairage doux — suffisent pourtant à la rendre élégante sans la surcharger.",
                'description' => <<<'HTML'
<p>Une chambre réussie ne se reconnaît pas au nombre d'objets qu'elle contient, mais à la sensation qu'elle procure dès qu'on pousse la porte. L'objectif est double : une pièce belle à regarder, et une pièce où l'on se détend vraiment. Voici comment y arriver, étape par étape.</p>

<h2>Commencer par une palette de couleurs apaisante</h2>
<p>Les teintes qui fonctionnent le mieux dans une chambre sont celles qui ne fatiguent pas l'œil : beige, lin, sable, blanc cassé, gris chaud, terracotta doux. Elles servent de fond neutre sur lequel tout le reste vient se poser.</p>
<p>Une règle pratique : gardez <strong>deux tons dominants</strong> pour les grandes surfaces (murs, literie, rideaux) et réservez une troisième couleur, plus soutenue, aux accessoires. C'est là qu'un coussin brodé ou un vase coloré prend toute sa valeur.</p>

<h2>Superposer les textiles, c'est là que naît la chaleur</h2>
<p>Une chambre paraît froide quand une seule matière occupe tout l'espace. La solution tient en un mot : la superposition. Une housse de couette en coton, un plaid en laine replié au pied du lit, deux ou trois coussins de tailles différentes, un tapis qui dépasse de chaque côté du lit — et la pièce change complètement.</p>

<h3>Le plaid, la pièce qui change tout</h3>
<p>C'est l'accessoire le plus rentable d'une chambre : il apporte de la matière, du relief et une vraie sensation de confort, tout en se rangeant en deux secondes. Un <a href="/article/plaid-laine-naturelle">plaid en laine naturelle</a> posé en diagonale sur le lit suffit à casser l'effet « chambre d'hôtel ».</p>
<p>Côté coussins, mieux vaut varier les formats que les couleurs : un carré 45×45, un rectangulaire et un plus petit créent déjà une composition. Retrouvez l'ensemble dans la collection <a href="/collections/textile-confort/coussins-plaids">Coussins &amp; plaids</a>.</p>

<h2>Travailler la lumière par couches</h2>
<p>Le plafonnier seul écrase les volumes et donne une lumière froide. Dans une chambre, on superpose plutôt trois sources :</p>
<ul>
  <li>un éclairage général au plafond, que l'on utilise finalement assez peu ;</li>
  <li>une lumière d'appoint de chaque côté du lit, à hauteur d'épaule une fois assis ;</li>
  <li>une lumière d'ambiance basse — une lampe posée sur une commode, une bougie — pour la fin de soirée.</li>
</ul>
<p>Une <a href="/article/lampe-artisanale-rotin">lampe artisanale en rotin</a> remplit très bien ce rôle : l'abat-jour tressé filtre la lumière et projette un motif sur le mur, ce qu'une ampoule nue ne fera jamais. Le reste de la sélection se trouve dans <a href="/collections/luminaires">Luminaires</a>.</p>

<h2>Choisir peu d'accessoires, mais les bons</h2>
<p>Trois ou quatre objets bien placés valent mieux qu'une dizaine dispersés. Pensez en groupes : un vase, un cadre et une bougie posés ensemble sur une commode forment un point de regard ; les mêmes objets répartis dans la pièce ne forment rien du tout.</p>

<h3>Le miroir, allié des chambres peu lumineuses</h3>
<p>Placé face à une fenêtre ou perpendiculairement à elle, un <a href="/article/miroir-rond-murale">miroir rond mural</a> renvoie la lumière du jour vers le fond de la pièce et agrandit visuellement l'espace. C'est souvent le geste le plus efficace dans une chambre orientée nord. Voir aussi la collection <a href="/collections/decoration-murale/miroirs-cadres">Miroirs &amp; cadres</a>.</p>

<h2>Une atmosphère qui se sent autant qu'elle se voit</h2>
<p>La décoration d'une chambre ne s'arrête pas au visuel. Une senteur discrète en fin de journée participe autant à l'ambiance qu'un bon éclairage. Les notes fraîches et végétales conviennent bien à cette pièce : elles apaisent sans saturer.</p>
<p>Un <a href="/article/diffuseur-bambou-verre">diffuseur en bambou et verre</a> diffuse en continu sans flamme, ce qui est plus sûr dans une chambre ; une <a href="/article/set-2-bougies-lavande">bougie à la lavande</a> se réserve plutôt au moment où l'on est présent dans la pièce. Toute la gamme est réunie dans <a href="/collections/senteurs-ambiance">Senteurs &amp; Ambiance</a>.</p>

<h2>À retenir</h2>
<ul>
  <li>Deux tons neutres dominants, une couleur d'accent sur les accessoires.</li>
  <li>Au moins trois matières différentes dans les textiles.</li>
  <li>Trois sources de lumière plutôt qu'un seul plafonnier.</li>
  <li>Des objets groupés par trois, jamais dispersés.</li>
  <li>Un miroir bien placé si la pièce manque de lumière naturelle.</li>
</ul>
<p>Aucun de ces changements ne demande de gros travaux. En commençant par les textiles et la lumière, on obtient déjà l'essentiel du résultat. <a href="/collections/textile-confort">Découvrir la collection Textile &amp; Confort</a>.</p>
HTML,
            ],
            [
                'slug' => 'inspiration-salon-moderne-confortable',
                'title' => '5 idées pour aménager un salon moderne et confortable',
                'meta_title' => 'Salon moderne et confortable : 5 idées à copier',
                'meta_description' => "Canapé, table basse, luminaires, décoration murale et textiles : cinq idées concrètes pour un salon à la fois contemporain et accueillant.",
                'image' => 'inspirations/creer-un-coin-bien-etre-a-la-maison-deco-lumiere-douce-et-rituels-detente-1783693172.webp',
                'resume' => "Un salon moderne n'est pas un salon vide. Ces cinq idées montrent comment garder des lignes contemporaines tout en obtenant une pièce dans laquelle on a réellement envie de s'installer.",
                'description' => <<<'HTML'
<p>Le salon est la pièce la plus exposée de la maison : c'est celle que voient les invités, et celle où la famille passe ses soirées. La difficulté est d'y concilier deux envies souvent contradictoires — une esthétique nette, contemporaine, et un confort qui donne envie de rester. Voici cinq idées qui répondent aux deux.</p>

<h2>1. Laisser respirer l'assise, puis la réchauffer</h2>
<p>Un canapé aux lignes simples est la base d'un salon moderne. Mais posé seul sur un sol nu, il paraît austère. Le réflexe efficace consiste à le laisser tel quel et à travailler ce qui l'entoure : coussins, plaid, tapis.</p>
<p>Comptez trois à cinq coussins pour un canapé trois places, en mélangeant les formats plutôt que les motifs. Un <a href="/article/coussin-brode-geometrique">coussin brodé géométrique</a> apporte du relief sans casser la sobriété de l'ensemble ; un <a href="/article/plaid-laine-naturelle">plaid en laine naturelle</a> jeté sur un accoudoir suffit à adoucir la silhouette.</p>

<h2>2. Choisir une table basse qui structure la pièce</h2>
<p>La table basse est l'élément qui fixe le centre du salon. Deux repères pour ne pas se tromper :</p>
<ul>
  <li>sa longueur doit couvrir environ les deux tiers de celle du canapé ;</li>
  <li>laissez 40 à 45 cm entre le canapé et la table pour circuler confortablement.</li>
</ul>
<p>Un piétement métal et un plateau bois — comme sur la <a href="/article/table-basse-industrielle">table basse industrielle</a> — donnent le vocabulaire contemporain tout en gardant la chaleur du bois. Voir aussi <a href="/collections/mobilier-rangement/tables-assises">Tables &amp; assises</a>.</p>

<h2>3. Multiplier les sources de lumière</h2>
<p>C'est le point qui différencie le plus un salon soigné d'un salon quelconque. Un plafonnier unique aplatit la pièce ; plusieurs points lumineux bas créent du relief et des zones.</p>
<h3>Une lumière par usage</h3>
<ul>
  <li>Une lumière de lecture près du fauteuil ou de l'extrémité du canapé.</li>
  <li>Une lumière d'ambiance sur un meuble bas ou une console.</li>
  <li>Une lumière d'appoint, souvent la plus basse, pour les fins de soirée.</li>
</ul>
<p>La <a href="/article/lampe-artisanale-rotin">lampe artisanale en rotin</a> fonctionne très bien pour les deux dernières : la matière tressée adoucit le faisceau. Le reste de la sélection est dans <a href="/collections/luminaires">Luminaires</a>.</p>

<h2>4. Habiller le mur sans le surcharger</h2>
<p>Un grand mur nu derrière le canapé est le défaut le plus fréquent. Deux approches fonctionnent bien :</p>
<ol>
  <li><strong>Une pièce forte, centrée</strong> — une <a href="/article/horloge-murale-bois-scandinave">horloge murale en bois scandinave</a> ou un miroir, suspendu à environ 150 cm du sol, centré sur le canapé et non sur le mur.</li>
  <li><strong>Une composition de cadres</strong> — trois à cinq <a href="/article/cadre-photo-moderne">cadres photo</a> alignés sur un axe, avec un espacement régulier de 5 à 8 cm.</li>
</ol>
<p>Toutes les options sont regroupées dans <a href="/collections/decoration-murale">Décoration murale</a>.</p>

<h2>5. Poser un tapis, même sur un beau sol</h2>
<p>Le tapis fait deux choses qu'aucun autre élément ne fait : il délimite la zone salon et il absorbe l'écho. Sa taille compte plus que son motif — les pieds avant du canapé doivent reposer dessus, sinon la pièce paraît coupée en deux.</p>
<p>Un <a href="/article/tapis-kilim-vintage">tapis kilim vintage</a> apporte en plus une trame et des couleurs qui évitent l'effet showroom. Voir la collection <a href="/collections/textile-confort/tapis">Tapis</a>.</p>

<h2>Et pour finir</h2>
<p>Une touche de végétal et une senteur discrète referment l'ensemble : un <a href="/article/pot-fleur-terre-cuite">pot en terre cuite</a> dans un angle, un <a href="/article/diffuseur-ceramique-bois">diffuseur céramique et bois</a> sur la console. Ce sont les derniers 10 % du travail, mais ceux que l'on remarque.</p>
<p><a href="/collections/mobilier-rangement">Parcourir la collection Mobilier &amp; Rangement</a></p>
HTML,
            ],
            [
                'slug' => 'inspiration-eclairage-piece-par-piece',
                'title' => 'Comment choisir son éclairage pièce par pièce',
                'meta_title' => 'Choisir son éclairage pièce par pièce : le guide',
                'meta_description' => "Salon, salle à manger, chambre : hauteur de suspension, dimensions, température de lumière. Les repères concrets pour éclairer juste.",
                'image' => 'inspirations/sublimez-votre-interieur-avec-des-lampes-decoratives-modernes-1769202080.webp',
                'resume' => "Hauteur, diamètre, température de lumière : l'éclairage obéit à quelques règles simples que l'on ignore souvent. Ce guide donne les repères pièce par pièce, du salon à la chambre.",
                'description' => <<<'HTML'
<p>On choisit souvent un luminaire pour son dessin, puis on découvre qu'il éclaire mal, qu'il est trop bas ou trop petit pour la pièce. Pourtant, les repères sont simples et se retiennent en quelques minutes. Voici ceux qui comptent vraiment.</p>

<h2>Les trois couches d'un bon éclairage</h2>
<p>Quelle que soit la pièce, un éclairage réussi combine trois niveaux :</p>
<ul>
  <li><strong>L'éclairage général</strong> — au plafond, il assure la luminosité de base.</li>
  <li><strong>L'éclairage fonctionnel</strong> — dirigé vers une activité précise : lecture, cuisine, bureau.</li>
  <li><strong>L'éclairage d'ambiance</strong> — bas, chaud, il crée l'atmosphère du soir.</li>
</ul>
<p>Une pièce qui ne dispose que du premier niveau paraît toujours un peu froide, même très bien décorée.</p>

<h2>Le salon</h2>
<p>C'est la pièce où la troisième couche compte le plus. Prévoyez au minimum deux points lumineux bas en plus du plafonnier : une lampe près de l'assise, une autre sur un meuble.</p>
<h3>À quelle hauteur suspendre ?</h3>
<p>Au-dessus d'un espace de circulation, le bas d'une suspension doit rester à <strong>2,10 m du sol</strong> au moins, pour que personne ne se cogne. Au-dessus d'une table basse, on peut descendre plus bas, mais le luminaire devient alors un objet décoratif à part entière.</p>
<p>Côté ambiance, une lampe à poser reste la solution la plus souple : elle se déplace, elle ne demande aucun travaux. La <a href="/article/lampe-artisanale-rotin">lampe artisanale en rotin</a> diffuse une lumière filtrée qui convient bien à cet usage. Voir la collection <a href="/collections/luminaires">Luminaires</a>.</p>

<h2>La salle à manger</h2>
<p>Ici, la lumière doit être centrée sur la table, pas sur la pièce. Deux repères suffisent :</p>
<ul>
  <li><strong>Hauteur</strong> : le bas du luminaire à 75 à 85 cm au-dessus du plateau. Plus haut, la lumière se disperse ; plus bas, elle gêne le regard des convives.</li>
  <li><strong>Diamètre</strong> : environ la moitié à un tiers de la largeur de la table. Un luminaire trop petit au-dessus d'une grande table déséquilibre visuellement la pièce.</li>
</ul>
<p>Pour une table rectangulaire longue, deux ou trois points alignés éclairent mieux qu'un seul luminaire central.</p>

<h2>La chambre</h2>
<p>La chambre demande l'éclairage le plus doux de la maison. Le plafonnier y sert surtout à faire le ménage — le reste du temps, on utilise des sources basses.</p>
<p>De chaque côté du lit, visez une lumière arrivant <strong>à hauteur d'épaule une fois assis</strong> : c'est la position confortable pour lire sans éblouir. Une bougie ou un diffuseur complètent l'ambiance en fin de journée ; voir <a href="/collections/senteurs-ambiance/bougies-parfumees">Bougies parfumées</a>.</p>

<h2>Chaude ou froide ? La question qu'on oublie</h2>
<p>La température de couleur se lit en kelvins sur l'emballage de l'ampoule et change tout :</p>
<ul>
  <li><strong>2 200 à 2 700 K</strong> — lumière chaude, ambrée. C'est celle du salon et de la chambre.</li>
  <li><strong>3 000 K</strong> — blanc chaud, polyvalent. Entrée, couloir, salle à manger.</li>
  <li><strong>4 000 K et plus</strong> — blanc neutre à froid. Réservé aux plans de travail et aux espaces techniques.</li>
</ul>
<p>Mélanger des températures très différentes dans une même pièce donne toujours un résultat bancal : mieux vaut harmoniser.</p>

<h2>Trois erreurs fréquentes</h2>
<ol>
  <li>Un seul plafonnier pour toute la pièce.</li>
  <li>Un luminaire sous-dimensionné par rapport à la table ou au volume.</li>
  <li>Des ampoules froides dans un espace de détente.</li>
</ol>

<h2>En pratique</h2>
<p>Commencez par ajouter une source basse dans la pièce où vous passez vos soirées. C'est le changement le plus rapide et le plus visible. Puis harmonisez les températures d'ampoules, pièce par pièce.</p>
<p><a href="/collections/luminaires">Voir les luminaires</a> — <a href="/collections/senteurs-ambiance">Voir Senteurs &amp; Ambiance</a></p>
HTML,
            ],
            [
                'slug' => 'inspiration-petite-maison-optimiser-espace',
                'title' => 'Décorer une petite maison sans perdre d\'espace',
                'meta_title' => 'Petit espace : décorer sans perdre de place',
                'meta_description' => "Rangement vertical, mobilier bien dimensionné, couleurs claires et miroirs : comment décorer un petit intérieur sans l'encombrer.",
                'image' => 'inspirations/creer-une-ambiance-dont-vos-clientes-se-souviennent-1786621042.webp',
                'resume' => "Dans un petit intérieur, chaque objet doit se justifier. Rangement en hauteur, mobilier à la bonne échelle, palette claire et miroirs bien placés : quatre leviers qui font gagner de la place sans rien sacrifier au style.",
                'description' => <<<'HTML'
<p>Décorer petit ne veut pas dire décorer moins. Cela veut dire choisir avec plus d'attention : chaque meuble occupe un pourcentage visible de la pièce, et chaque objet en trop se remarque immédiatement. Voici la méthode qui fonctionne.</p>

<h2>Monter, plutôt que s'étaler</h2>
<p>Le premier réflexe dans un petit espace est d'utiliser la hauteur. Un rangement mural libère toute la surface au sol qu'aurait occupée un meuble bas — à volume de stockage équivalent.</p>
<p>Une <a href="/article/bibliotheque-etagere-murale">bibliothèque étagère murale</a> fixée au-dessus d'un bureau ou d'un canapé exploite une zone perdue, et déplace le regard vers le haut, ce qui donne une impression de hauteur sous plafond. Voir la collection <a href="/collections/mobilier-rangement/etageres-rangement">Étagères &amp; rangement</a>.</p>
<h3>Une règle utile</h3>
<p>Ne remplissez jamais une étagère à 100 %. Autour de <strong>deux tiers d'occupation</strong>, l'ensemble paraît rangé ; au-delà, il paraît encombré, même parfaitement ordonné.</p>

<h2>Dimensionner le mobilier à la pièce</h2>
<p>L'erreur la plus courante dans un petit intérieur n'est pas d'avoir trop de meubles, mais d'en avoir de trop gros. Quelques repères :</p>
<ul>
  <li>Préférez les meubles sur pieds : voir le sol sous un meuble agrandit visuellement la pièce.</li>
  <li>Gardez <strong>60 cm minimum</strong> pour chaque passage, 80 cm pour un passage principal.</li>
  <li>Une assise compacte et basse encombre moins qu'un fauteuil enveloppant, à confort proche.</li>
</ul>
<p>Une <a href="/article/chaise-scandinave-assise-cuir">chaise scandinave sur piétement fin</a> illustre bien ce principe : elle occupe la même surface au sol qu'une chaise massive, mais paraît deux fois plus légère.</p>

<h2>Jouer la palette claire — sans tomber dans le tout blanc</h2>
<p>Les couleurs claires renvoient la lumière et repoussent visuellement les murs. Mais une pièce entièrement blanche paraît froide et, paradoxalement, plus petite, faute de relief.</p>
<p>La solution : garder les grandes surfaces claires et introduire la matière par les textiles et les accessoires. Un <a href="/article/tapis-kilim-vintage">tapis kilim</a> au sol, un <a href="/article/coussin-brode-geometrique">coussin brodé</a>, un <a href="/article/vase-minimaliste-ceramique">vase en céramique</a> suffisent à donner de la profondeur.</p>

<h2>Les miroirs, à condition de bien les placer</h2>
<p>Un miroir n'agrandit pas une pièce par magie : il agrandit ce qu'il reflète. Placé face à un mur nu, il ne fait rien. Placé face ou perpendiculairement à une fenêtre, il double la lumière entrante et ouvre la perspective.</p>
<p>Un <a href="/article/miroir-rond-murale">miroir rond mural</a> est en général plus facile à intégrer qu'un miroir rectangulaire dans un petit espace : il adoucit les angles d'une pièce déjà très cadrée. Voir <a href="/collections/decoration-murale/miroirs-cadres">Miroirs &amp; cadres</a>.</p>

<h2>Limiter le nombre de points de regard</h2>
<p>Dans une grande pièce, on peut multiplier les zones décorées. Dans une petite, chaque nouvelle zone fragmente l'espace. Fixez-vous <strong>deux ou trois points forts maximum</strong> par pièce : un mur habillé, une étagère composée, un coin végétal.</p>
<p>Regroupez le reste. Trois objets réunis sur un plateau forment un ensemble ; les mêmes trois objets posés à trois endroits différents forment du désordre.</p>

<h2>La liste de contrôle</h2>
<ul>
  <li>Le rangement monte-t-il au mur plutôt que de s'étaler au sol ?</li>
  <li>Voit-on le sol sous les meubles principaux ?</li>
  <li>Les passages font-ils au moins 60 cm ?</li>
  <li>Les miroirs reflètent-ils une source de lumière ?</li>
  <li>Y a-t-il moins de quatre points de regard dans la pièce ?</li>
</ul>
<p>Si la réponse est oui aux cinq questions, la pièce paraîtra plus grande qu'elle ne l'est — sans avoir rien enlevé d'essentiel.</p>
<p><a href="/collections/mobilier-rangement">Voir Mobilier &amp; Rangement</a> — <a href="/collections/decoration-murale">Voir Décoration murale</a></p>
HTML,
            ],
            [
                'slug' => 'inspiration-tendances-decoration-interieure',
                'title' => 'Les tendances décoration intérieure pour une maison élégante',
                'meta_title' => 'Tendances déco : matières naturelles et tons neutres',
                'meta_description' => "Bois, céramique, fibres tressées, palette neutre et lumière travaillée : les tendances déco qui vieillissent bien, et comment les adopter chez soi.",
                'image' => 'inspirations/creer-un-coin-bien-etre-a-la-maison-deco-lumiere-douce-et-rituels-detente-1783693194.webp',
                'resume' => "Les tendances intéressantes sont celles qui durent. Matières naturelles, tons neutres, artisanat et lumière chaude forment une base qui ne se démodera pas dans deux saisons.",
                'description' => <<<'HTML'
<p>Suivre les tendances a mauvaise presse en décoration, et souvent à juste titre : une pièce entièrement construite autour d'une mode paraît datée très vite. Mais certaines évolutions de fond sont différentes — elles reposent sur des matières et des principes qui traversent les années. Ce sont celles-là qui méritent qu'on s'y arrête.</p>

<h2>Les matières naturelles reprennent la main</h2>
<p>Bois brut, céramique, terre cuite, fibres tressées, laine : le point commun de ces matières est qu'elles vieillissent bien. Une céramique se patine, un bois se nuance, une fibre naturelle se détend — là où un plastique brillant se raye et se démode.</p>
<p>Concrètement, cela se traduit par des choix simples : un <a href="/article/pot-fleur-terre-cuite">pot en terre cuite</a> plutôt qu'un cache-pot laqué, un <a href="/article/diffuseur-bambou-verre">diffuseur en bambou et verre</a> plutôt qu'un boîtier chromé, un <a href="/article/plaid-laine-naturelle">plaid en laine</a> plutôt qu'une couverture synthétique.</p>
<h3>Mélanger les matières, pas les styles</h3>
<p>La règle qui fonctionne : associer <strong>trois matières différentes</strong> dans un même regard — par exemple bois, céramique et textile — mais garder une seule ligne esthétique. C'est le mélange des matières qui crée la richesse, pas le mélange des styles.</p>

<h2>La palette neutre, mais pas plate</h2>
<p>Les tons neutres dominent depuis plusieurs saisons, et ils n'ont pas fini de durer : ils se marient avec tout et supportent d'être conservés dix ans. La nuance qui compte est qu'un neutre n'est pas forcément un beige uniforme.</p>
<ul>
  <li>Des neutres <strong>chauds</strong> — sable, lin, argile, écru — pour une atmosphère enveloppante.</li>
  <li>Des accents <strong>terreux</strong> — terracotta, ocre, vert olive — en petites touches sur les accessoires.</li>
  <li>Un <strong>point sombre</strong> par pièce — un cadre noir, un piétement métal — pour donner du contraste et éviter l'effet fade.</li>
</ul>
<p>Une <a href="/article/table-basse-industrielle">table basse au piétement métal</a> joue très bien ce rôle de point d'ancrage dans une pièce claire.</p>

<h2>L'artisanat et la pièce imparfaite</h2>
<p>La tendance de fond la plus nette de ces dernières années est le retour de l'objet fait main. Une pièce artisanale porte de légères irrégularités : c'est précisément ce qui la distingue et ce qui donne de la personnalité à un intérieur.</p>
<p>Un <a href="/article/vase-minimaliste-ceramique">vase en céramique</a>, un <a href="/article/set-3-plats-ceramique">set de plats</a> ou une <a href="/article/lampe-artisanale-rotin">lampe en rotin tressé</a> apportent cette dimension que le mobilier industriel ne peut pas produire. Voir <a href="/collections/art-de-la-table-vases">Art de la table &amp; Vases</a>.</p>

<h2>La lumière traitée comme un élément de décoration</h2>
<p>C'est sans doute le changement le plus important : la lumière n'est plus seulement technique. On ne se demande plus « est-ce que la pièce est éclairée ? » mais « comment est-elle éclairée ? ».</p>
<p>En pratique, cela signifie plusieurs sources basses et chaudes plutôt qu'un plafonnier unique, des ampoules autour de 2 700 K dans les espaces de vie, et l'usage assumé de la flamme — une <a href="/collections/senteurs-ambiance/bougies-parfumees">bougie parfumée</a> reste la source d'ambiance la plus simple qui soit.</p>

<h2>Le végétal, devenu structurel</h2>
<p>La plante n'est plus un accessoire de dernière minute : elle occupe une place dans le plan de la pièce, souvent dans un angle laissé vide ou sur une étagère haute. Un grand pot posé au sol vaut visuellement un meuble, pour un encombrement bien moindre.</p>

<h2>Ce qu'il faut en retenir</h2>
<ol>
  <li>Privilégier les matières qui se patinent plutôt que celles qui s'usent.</li>
  <li>Construire sur une base neutre, et changer les accents plutôt que les meubles.</li>
  <li>Introduire au moins une pièce artisanale par pièce de vie.</li>
  <li>Travailler la lumière avant d'ajouter de la décoration.</li>
</ol>
<p>Une maison élégante n'est pas une maison à la mode : c'est une maison cohérente, où les matières se répondent et où la lumière est juste. Le reste se change au fil des envies.</p>
<p><a href="/toutes/produits">Parcourir toute la collection</a></p>
HTML,
            ],
        ];
    }
}
