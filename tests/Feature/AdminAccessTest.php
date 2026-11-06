<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<string> */
    private function adminUrls(): array
    {
        return [
            '/admin/paradis-deco/home',
            '/admin/paradis-deco/dashborad',
            '/admin/paradis-deco/produits',
            '/admin/paradis-deco/categories',
            '/admin/paradis-deco/commandes',
            '/admin/paradis-deco/configurations',
            '/admin/paradis-deco/avis',
            '/admin/paradis-deco/inspirations',
        ];
    }

    public function test_un_visiteur_anonyme_est_redirige_vers_le_login(): void
    {
        foreach ($this->adminUrls() as $url) {
            $this->get($url)->assertRedirect('/admin/paradis-deco/login');
        }
    }

    public function test_un_utilisateur_sans_role_superadmin_est_refuse(): void
    {
        $user = User::create([
            'name' => 'simple',
            'email' => 'simple@example.test',
            'password' => bcrypt('secret'),
        ]);

        foreach ($this->adminUrls() as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_un_superadmin_accede_au_back_office(): void
    {
        Role::findOrCreate('superadmin');

        $admin = tap(User::create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
        ]))->assignRole('superadmin');

        $this->actingAs($admin)->get('/admin/paradis-deco/dashborad')->assertOk();
    }

    /** Une catégorie ne peut pas être modifiée sans authentification. */
    public function test_une_categorie_ne_peut_pas_etre_modifiee_par_un_anonyme(): void
    {
        $category = Category::create([
            'name' => 'Senteurs',
            'image' => 'x.webp',
            'is_active' => true,
        ]);

        $this->put("/admin/paradis-deco/categories/{$category->id}", ['name' => 'Piraté', 'is_active' => 1])
            ->assertRedirect('/admin/paradis-deco/login');

        $this->delete("/admin/paradis-deco/categories/{$category->id}")
            ->assertRedirect('/admin/paradis-deco/login');

        $this->assertSame('Senteurs', $category->refresh()->name);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);
    }
}
