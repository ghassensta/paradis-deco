<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * D'anciennes écritures ont appliqué json_encode() à des colonnes qui
     * portent déjà le cast 'array' : la valeur stockée est alors une CHAÎNE
     * JSON contenant le tableau, au lieu du tableau lui-même.
     *
     * La condition ne touche QUE les lignes dont le déquotage produit un JSON
     * valide de type ARRAY. Sont donc laissées intactes :
     *   - les tableaux déjà sains (JSON_TYPE = ARRAY)
     *   - NULL
     *   - les objets et les scalaires
     *   - les chaînes qui ne sont pas du JSON (ex. "mon-fichier.jpg")
     *
     * Rejouer la migration ne change plus rien : elle est idempotente.
     */
    private const COLUMNS = ['images', 'category_ids'];

    public function up(): void
    {
        foreach (self::COLUMNS as $column) {
            $repaired = DB::transaction(fn () => DB::update(
                "UPDATE products
                    SET `{$column}` = CAST(JSON_UNQUOTE(`{$column}`) AS JSON)
                  WHERE JSON_TYPE(`{$column}`) = 'STRING'
                    AND JSON_VALID(JSON_UNQUOTE(`{$column}`))
                    AND JSON_TYPE(CAST(JSON_UNQUOTE(`{$column}`) AS JSON)) = 'ARRAY'"
            ));

            Log::info("Réparation JSON products.{$column} : {$repaired} ligne(s) corrigée(s).");
        }
    }

    /**
     * Pas de rollback : réencoder volontairement les données pour recréer la
     * corruption n'aurait aucun sens. Les accesseurs du modèle Product
     * continuent d'absorber les deux formes, la migration est donc sans risque
     * de régression si elle est annulée.
     */
    public function down(): void
    {
        //
    }
};
