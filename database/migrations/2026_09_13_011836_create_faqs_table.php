<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table polymorphe unique plutôt que product_faqs + category_faqs :
     * les deux auraient eu exactement les mêmes colonnes, la même
     * validation, la même interface d'administration et le même rendu.
     * L'indépendance entre les deux jeux de FAQ est assurée par faqable_type.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->morphs('faqable');            // faqable_type + faqable_id (indexés)
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['faqable_type', 'faqable_id', 'is_active', 'position'], 'faqs_display_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
