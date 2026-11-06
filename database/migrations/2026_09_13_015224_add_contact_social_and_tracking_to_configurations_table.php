<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Les vues du front lisent déjà $config->support_phone, ->address,
     * ->facebook_url, etc. : ces colonnes n'existaient pas, l'accès renvoyait
     * silencieusement null et le pied de page affichait du vide.
     *
     * On ajoute aussi la section tracking (Meta Pixel, TikTok, GA4), désactivée
     * par défaut : aucun script n'est injecté tant qu'un identifiant n'est pas
     * saisi ET l'activation cochée.
     *
     * Aucune colonne existante n'est renommée ni supprimée.
     */
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            // Contact
            $table->string('support_phone')->nullable()->after('support_email');
            $table->string('address')->nullable()->after('support_phone');
            $table->string('city')->nullable()->after('address');
            $table->string('working_hours')->nullable()->after('city');

            // Réseaux sociaux
            $table->string('facebook_url')->nullable()->after('working_hours');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('tiktok_url')->nullable()->after('instagram_url');
            $table->string('whatsapp_number')->nullable()->after('tiktok_url');

            // SEO
            $table->string('og_image')->nullable()->after('meta_description');

            // Tracking — chaque tracker a son identifiant et son interrupteur.
            $table->string('meta_pixel_id')->nullable()->after('homepage_banner');
            $table->boolean('meta_pixel_enabled')->default(false)->after('meta_pixel_id');
            $table->string('tiktok_pixel_id')->nullable()->after('meta_pixel_enabled');
            $table->boolean('tiktok_pixel_enabled')->default(false)->after('tiktok_pixel_id');
            $table->string('ga4_measurement_id')->nullable()->after('tiktok_pixel_enabled');
            $table->boolean('ga4_enabled')->default(false)->after('ga4_measurement_id');
        });
    }

    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'support_phone', 'address', 'city', 'working_hours',
                'facebook_url', 'instagram_url', 'tiktok_url', 'whatsapp_number',
                'og_image',
                'meta_pixel_id', 'meta_pixel_enabled',
                'tiktok_pixel_id', 'tiktok_pixel_enabled',
                'ga4_measurement_id', 'ga4_enabled',
            ]);
        });
    }
};
