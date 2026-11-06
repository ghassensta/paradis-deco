<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillProductShortDescriptions extends Command
{
    protected $signature = 'products:backfill-short-descriptions
                            {--dry-run : Affiche ce qui serait écrit sans rien modifier}
                            {--limit=0 : Limite le nombre de fiches traitées}
                            {--max=300 : Longueur maximale du résumé généré}';

    protected $description = "Renseigne description_courte à partir des premières phrases de la description longue";

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $max = max(80, (int) $this->option('max'));
        $limit = (int) $this->option('limit');

        $query = Product::query()
            ->whereNull('description_courte')
            ->whereNotNull('description')
            ->where('description', '!=', '');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get(['id', 'name', 'description', 'description_courte']);

        if ($products->isEmpty()) {
            $this->info('Aucune fiche à compléter.');

            return self::SUCCESS;
        }

        $written = 0;
        $skipped = 0;

        foreach ($products as $product) {
            $summary = $this->summarize($product->description, $max);

            // Si la description longue tient déjà dans la limite, le résumé
            // serait identique : le repli d'affichage suffit, on n'écrit rien.
            if ($summary === null) {
                $skipped++;
                continue;
            }

            $this->line(sprintf('  #%d %s', $product->id, Str::limit($product->name, 40)));
            $this->line(sprintf('     → %s', $summary));

            if (! $dryRun) {
                $product->update(['description_courte' => $summary]);
            }

            $written++;
        }

        $this->newLine();
        $this->info(sprintf(
            '%s : %d résumé(s) %s, %d ignorée(s) (description déjà assez courte).',
            $dryRun ? 'Simulation' : 'Terminé',
            $written,
            $dryRun ? 'à écrire' : 'écrit(s)',
            $skipped
        ));

        if ($dryRun) {
            $this->comment('Relancez sans --dry-run pour appliquer.');
        }

        return self::SUCCESS;
    }

    /**
     * Extrait les premières phrases complètes de la description longue.
     * Aucun contenu n'est inventé : le texte provient uniquement de la fiche.
     * Retourne null lorsqu'un résumé n'apporterait rien.
     */
    private function summarize(string $description, int $max): ?string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));

        if ($text === '' || mb_strlen($text) <= $max) {
            return null;
        }

        $summary = '';

        foreach (preg_split('/(?<=[.!?])\s+/u', $text) as $sentence) {
            $candidate = trim($summary . ' ' . $sentence);

            if (mb_strlen($candidate) > $max) {
                break;
            }

            $summary = $candidate;
        }

        // Aucune phrase complète ne tient : on tronque proprement sur un mot.
        return $summary !== '' ? $summary : Str::limit($text, $max - 1);
    }
}
