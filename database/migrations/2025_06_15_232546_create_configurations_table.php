<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
         $table->id();

            // Informations générales
            $table->string('site_name')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('support_email')->nullable();
            $table->string('default_language')->default('fr');
            $table->string('currency')->default('DT');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Livraison
            $table->decimal('shipping_cost', 8, 3)->default(7.000); // Ex: 7.000 TND
            $table->decimal('free_shipping_threshold', 8, 3)->default(100.000);
            $table->integer('delivery_estimate_days')->default(2);

            // Divers
            $table->boolean('maintenance_mode')->default(false);
            $table->string('homepage_banner')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
