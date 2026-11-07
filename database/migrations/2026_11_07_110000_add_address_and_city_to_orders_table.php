<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L'adresse vivait seulement sur le client, retrouvé par téléphone : un
     * client qui recommandait depuis une nouvelle adresse était livré à
     * l'ancienne. Chaque commande garde désormais l'adresse saisie.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('city', 50)->nullable();
        });

        // Les prix sont saisis et affichés en dinars partout sur le site : une
        // autre devise faussait seulement les montants envoyés aux pixels.
        DB::table('configurations')->where('currency', '!=', 'TND')->update(['currency' => 'TND']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'city']);
        });
    }
};
