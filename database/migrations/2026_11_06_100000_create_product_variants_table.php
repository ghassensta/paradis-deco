<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Une variante = une combinaison vendable (type et/ou taille) d'une même
     * fiche produit, avec son prix, son stock et son image.
     *
     * products.has_variants évite une requête par carte dans les listes ;
     * products.price et products.stock y restent le prix minimum et le stock
     * cumulé, tenus à jour par App\Support\VariantSync.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100)->nullable();
            $table->string('size', 100)->nullable();
            $table->decimal('price', 10, 3);
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_variants')->default(false);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            // Copie du libellé : la commande reste lisible si la variante disparaît.
            $table->string('variant_label')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
            $table->dropColumn('variant_label');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_variants');
        });

        Schema::dropIfExists('product_variants');
    }
};
