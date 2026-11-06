<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sépare le contenu produit en deux champs :
     *   - description        : description longue (colonne existante, inchangée)
     *   - description_courte : résumé affiché près du prix et du bouton d'achat
     *
     * Aucune donnée n'est déplacée ni écrasée : la colonne est simplement
     * ajoutée en NULL. Les fiches existantes continuent de s'afficher grâce au
     * repli défini sur le modèle (Product::short_description).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description_courte')
                ->nullable()
                ->after('description')
                ->comment('Résumé court affiché près du prix (150-300 caractères recommandés)');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('description_courte');
        });
    }
};
