<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * L'identifiant Google Analytics G-404BKDSJRD était écrit en dur dans le
     * layout public : il échappait au réglage en back-office et au futur
     * consentement. On le reporte dans la configuration pour que la mesure
     * existante continue exactement comme avant, mais pilotable.
     *
     * Une configuration qui a déjà un identifiant n'est pas écrasée.
     */
    private const LEGACY_ID = 'G-404BKDSJRD';

    public function up(): void
    {
        DB::table('configurations')
            ->where(fn ($q) => $q->whereNull('ga4_measurement_id')->orWhere('ga4_measurement_id', ''))
            ->update([
                'ga4_measurement_id' => self::LEGACY_ID,
                'ga4_enabled' => true,
            ]);
    }

    public function down(): void
    {
        DB::table('configurations')
            ->where('ga4_measurement_id', self::LEGACY_ID)
            ->update(['ga4_measurement_id' => null, 'ga4_enabled' => false]);
    }
};
