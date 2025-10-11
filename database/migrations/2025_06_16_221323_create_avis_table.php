<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Produit associé
            $table->unsignedTinyInteger('rating')->between(1, 5); // Note de 1 à 5 étoiles
            $table->text('comment')->nullable(); // Commentaire facultatif
            $table->string('name'); // Nom de l'auteur (obligatoire)
            $table->string('location')->nullable(); // Localisation (optionnel)
            $table->boolean('approved')->default(false); // Statut d'approbation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
