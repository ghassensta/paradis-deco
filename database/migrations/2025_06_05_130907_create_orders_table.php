<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
   {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            /* Référence interne de la commande */
            $table->string('numero_commande')
                  ->unique()
                  ->comment('Ex. CMD-ABC123');

            /* Relations */
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->cascadeOnDelete();

            $table->foreignId('statut_id')
                  ->constrained('statuts')
                  ->cascadeOnDelete();

            /* Montants */
            $table->decimal('subtotal_ht',   10, 2)   ->comment('Total HT du panier');
            $table->decimal('shipping_cost', 10, 2)   ->default(0)->comment('Frais de port');
            $table->decimal('tax_rate',       5, 2)   ->default(0)->comment('Taux TVA (ex. 19.00)');
            $table->decimal('tax_tva',       10, 2)   ->default(0)->comment('Montant TVA');
            $table->decimal('total_ttc',     10, 2)   ->comment('Total TTC');

            /* Paiement / expédition */
            $table->string('payment_method')
                  ->default('Paiement à la Livraison');

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            /* Soft delete + timestamps */
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
