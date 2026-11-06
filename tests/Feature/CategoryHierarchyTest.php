<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    private function root(string $name = 'Senteurs'): Category
    {
        return Category::create([
            'name' => $name,
            'image' => 'categories/demo.webp',
            'is_active' => true,
        ]);
    }

    private function child(Category $parent, string $name = 'Bougies'): Category
    {
        return Category::create([
            'parent_id' => $parent->id,
            'name' => $name,
            'is_active' => true,
        ]);
    }

    /** TEST 1 */
    public function test_une_categorie_peut_avoir_plusieurs_sous_categories(): void
    {
        $root = $this->root();
        $this->child($root, 'Bougies parfumées');
        $this->child($root, 'Diffuseurs');

        $this->assertCount(2, $root->refresh()->children);
        $this->assertTrue($root->isRoot());
    }

    /** TEST 2 */
    public function test_une_sous_categorie_appartient_a_un_parent(): void
    {
        $root = $this->root();
        $child = $this->child($root);

        $this->assertSame($root->id, $child->parent->id);
        $this->assertFalse($child->isRoot());
    }

    /** TEST 3 */
    public function test_une_categorie_ne_peut_pas_etre_son_propre_parent(): void
    {
        $root = $this->root();

        $this->assertFalse($root->canHaveParent($root->id));
    }

    /** TEST 3 bis : pas de hiérarchie à trois niveaux ni de boucle */
    public function test_une_sous_categorie_ne_peut_pas_devenir_son_propre_ancetre(): void
    {
        $root = $this->root();
        $child = $this->child($root);
        $other = $this->root('Décoration');

        // Rattacher une catégorie sous une sous-catégorie est refusé.
        $this->assertFalse($other->canHaveParent($child->id));

        // Une catégorie qui a déjà des enfants ne peut pas devenir enfant.
        $this->assertFalse($root->canHaveParent($other->id));
    }

    /** TEST 9 */
    public function test_les_slugs_sont_uniques(): void
    {
        $a = $this->root('Tapis');
        $b = Category::create(['name' => 'Tapis', 'image' => 'x.webp', 'is_active' => true]);

        $this->assertNotSame($a->slug, $b->slug);
        $this->assertSame('tapis', $a->slug);
        $this->assertSame('tapis-2', $b->slug);
    }

    /** La relation products() suit bien la colonne JSON category_ids. */
    public function test_les_produits_sont_rattaches_via_category_ids(): void
    {
        $root = $this->root();
        $child = $this->child($root);

        $product = Product::create([
            'name' => 'Bougie test',
            'slug' => 'bougie-test',
            'description' => 'Test',
            'price' => 10,
            'stock' => 5,
            'is_active' => true,
            'category_ids' => [$root->id, $child->id],
            'images' => [],
        ]);

        $this->assertSame(1, $child->products()->count());
        $this->assertSame($child->id, $product->primaryCategory()->id);
    }
}
