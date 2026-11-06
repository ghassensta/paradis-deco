<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clé générée par le navigateur pour une tentative de commande. L'index
     * unique garantit une seule commande, même pour deux envois simultanés.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('checkout_key', 64)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['checkout_key']);
            $table->dropColumn('checkout_key');
        });
    }
};
