<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Le champ « résumé » est saisi via un textarea : varchar(255) débordait.
     */
    public function up(): void
    {
        Schema::table('inspirations', function (Blueprint $table) {
            $table->text('resume')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inspirations', function (Blueprint $table) {
            $table->string('resume')->nullable()->change();
        });
    }
};
