<?php

namespace Tests;

use App\Models\User;
use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--seed' => true]);
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Test User',
            'status' => UserStatusEnum::ACTIVE->value,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function createSuperAdmin(): User
    {
        $user = $this->createUser([
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);

        $role = \App\Models\Role::where('name', 'super_admin')->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return $user->fresh()->load('roles.permissions');
    }

    protected function createAdminWithPermission(string $permissionName): User
    {
        $user = $this->createUser([
            'username' => 'admin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
        ]);

        $permission = \App\Models\Permission::where('name', $permissionName)->first();
        if ($permission) {
            $role = \App\Models\Role::create([
                'name' => 'test_role_' . uniqid(),
                'display_name' => 'Test Role',
                'guard_name' => 'web',
            ]);
            $role->permissions()->sync([$permission->id]);
            $user->roles()->sync([$role->id]);
        }

        return $user->fresh()->load('roles.permissions');
    }

    protected function actingAsSuperAdmin(): User
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    protected function actingAsWithPermission(string $permissionName): User
    {
        $user = $this->createAdminWithPermission($permissionName);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    protected function assertJsonStructureForList(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJsonStructure([
            'success',
            'data' => [],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
    }

    protected function assertJsonStructureForDetail(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJsonStructure([
            'success',
            'data' => [],
        ]);
    }

    protected function assertJsonSuccess(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJson(['success' => true]);
    }
}
