<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_roles()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/roles");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_roles()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/roles");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_roles_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/roles/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_role()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/roles", [
            'name' => 'test-role',
            'display_name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'display_name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', ['name' => 'test-role']);
    }

    /** @test */
    public function it_validates_create_role_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/roles", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'display_name']);
    }

    /** @test */
    public function it_can_show_role()
    {
        $this->actingAsSuperAdmin();
        $role = Role::first();

        $response = $this->getJson("{$this->baseUrl}/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'display_name', 'permissions'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_role()
    {
        $this->actingAsSuperAdmin();
        $role = Role::create([
            'name' => 'updatable-role',
            'display_name' => 'Updatable Role',
            'guard_name' => 'web',
        ]);

        $response = $this->putJson("{$this->baseUrl}/roles/{$role->id}", [
            'name' => 'updated-role',
            'display_name' => 'Updated Role',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', ['name' => 'updated-role']);
    }

    /** @test */
    public function it_can_delete_role()
    {
        $this->actingAsSuperAdmin();
        $role = Role::create([
            'name' => 'deletable-role',
            'display_name' => 'Deletable Role',
            'guard_name' => 'web',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }

    /** @test */
    public function it_can_restore_role()
    {
        $this->actingAsSuperAdmin();
        $role = Role::create([
            'name' => 'restorable-role',
            'display_name' => 'Restorable Role',
            'guard_name' => 'web',
        ]);
        $role->delete();

        $response = $this->postJson("{$this->baseUrl}/roles/{$role->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'deleted_at' => null,
        ]);
    }
}
