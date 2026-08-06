<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RolePermission;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_permissions_page_and_update_permissions()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.permissions'));
        $response->assertStatus(200);
        $response->assertSee('Gestion des Accès');

        // Update permissions for commercial
        $response = $this->actingAs($admin)->post(route('admin.users.permissions.update'), [
            'permissions' => [
                'commercial' => ['services', 'projects']
            ]
        ]);

        $response->assertRedirect(route('admin.users.permissions'));
        
        $rolePermission = RolePermission::where('role', 'commercial')->first();
        $this->assertNotNull($rolePermission);
        $this->assertEquals(['services', 'projects'], $rolePermission->permissions);
    }

    public function test_staff_user_restricted_by_permissions()
    {
        // 1. Create a commercial staff user
        $commercial = User::factory()->create([
            'role' => 'commercial',
            'is_admin' => false,
        ]);

        // 2. Set specific permissions for commercial
        RolePermission::create([
            'role' => 'commercial',
            'permissions' => ['services']
        ]);

        // 3. Try to access services index (permitted)
        $response = $this->actingAs($commercial)->get(route('admin.services.index'));
        $response->assertStatus(200);

        // 4. Try to access invoices index (forbidden)
        $response = $this->actingAs($commercial)->get(route('admin.invoices.index'));
        $response->assertStatus(403);
    }

    public function test_admin_has_all_permissions_by_default()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        // Admin can access everything regardless of role_permissions table
        $response = $this->actingAs($admin)->get(route('admin.invoices.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.services.index'));
        $response->assertStatus(200);
    }
}
