<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La colonne `description` existante devient la description longue
     * (éditée avec Quill) ; on lui adjoint un résumé court, sur le même
     * modèle que les fiches produits. Aucune donnée n'est déplacée.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description_courte')
                ->nullable()
                ->after('slug')
                ->comment('Résumé court affiché en tête de page (150-300 caractères recommandés)');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description_courte');
        });
    }
};
