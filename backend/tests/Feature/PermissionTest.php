<?php

namespace Tests\Feature;

use App\Models\Permission;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_permissions()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/permissions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_permissions()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/permissions");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_permissions_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/permissions/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_permission()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/permissions", [
            'name' => 'test-permission',
            'display_name' => 'Test Permission',
            'guard_name' => 'web',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'display_name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('permissions', ['name' => 'test-permission']);
    }

    /** @test */
    public function it_validates_create_permission_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/permissions", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'display_name']);
    }

    /** @test */
    public function it_can_show_permission()
    {
        $this->actingAsSuperAdmin();
        $permission = Permission::first();

        $response = $this->getJson("{$this->baseUrl}/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'display_name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_permission()
    {
        $this->actingAsSuperAdmin();
        $permission = Permission::create([
            'name' => 'updatable-permission',
            'display_name' => 'Updatable Permission',
            'guard_name' => 'web',
        ]);

        $response = $this->putJson("{$this->baseUrl}/permissions/{$permission->id}", [
            'name' => 'updated-permission',
            'display_name' => 'Updated Permission',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('permissions', ['name' => 'updated-permission']);
    }

    /** @test */
    public function it_can_delete_permission()
    {
        $this->actingAsSuperAdmin();
        $permission = Permission::create([
            'name' => 'deletable-permission',
            'display_name' => 'Deletable Permission',
            'guard_name' => 'web',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function it_can_restore_permission()
    {
        $this->actingAsSuperAdmin();
        $permission = Permission::create([
            'name' => 'restorable-permission',
            'display_name' => 'Restorable Permission',
            'guard_name' => 'web',
        ]);
        $permission->delete();

        $response = $this->postJson("{$this->baseUrl}/permissions/{$permission->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'deleted_at' => null,
        ]);
    }
}
