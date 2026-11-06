<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hiérarchie à un niveau : parent_id NULL = catégorie principale,
     * parent_id renseigné = sous-catégorie.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->nullOnDelete();

            $table->text('description')->nullable()->after('slug');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');

            // Une sous-catégorie n'a pas toujours d'illustration propre.
            $table->string('image')->nullable()->change();

            $table->index(['parent_id', 'is_active', 'sort_order'], 'categories_tree_index');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_tree_index');
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'description', 'sort_order']);
            $table->string('image')->nullable(false)->change();
        });
    }
};
