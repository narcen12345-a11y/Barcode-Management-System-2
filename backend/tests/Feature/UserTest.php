<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_users()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/users");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_users()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/users");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_create_user()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/users", [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'full_name' => 'New User',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'username', 'email', 'full_name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);
    }

    /** @test */
    public function it_validates_create_user_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/users", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'email', 'password', 'full_name']);
    }

    /** @test */
    public function it_can_show_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'showuser', 'email' => 'show@example.com']);

        $response = $this->getJson("{$this->baseUrl}/users/{$target->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'username', 'email', 'full_name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/users/99999");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'updateuser', 'email' => 'update@example.com']);

        $response = $this->putJson("{$this->baseUrl}/users/{$target->id}", [
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'full_name' => 'Updated User',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'username' => 'updateduser',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'deleteuser', 'email' => 'delete@example.com']);

        $response = $this->deleteJson("{$this->baseUrl}/users/{$target->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    /** @test */
    public function it_can_restore_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'restoreuser', 'email' => 'restore@example.com']);
        $target->delete();

        $response = $this->postJson("{$this->baseUrl}/users/{$target->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_activate_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser([
            'username' => 'activateuser',
            'email' => 'activate@example.com',
            'is_active' => false,
            'status' => \App\Enums\UserStatusEnum::INACTIVE->value,
        ]);

        $response = $this->postJson("{$this->baseUrl}/users/{$target->id}/activate");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_deactivate_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'deactivateuser', 'email' => 'deactivate@example.com']);

        $response = $this->postJson("{$this->baseUrl}/users/{$target->id}/deactivate");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_can_reset_password()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser(['username' => 'resetpwduser', 'email' => 'resetpwd@example.com']);

        $response = $this->postJson("{$this->baseUrl}/users/{$target->id}/reset-password");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonMissing(['data' => ['new_password']]);
    }

    /** @test */
    public function it_can_verify_user()
    {
        $this->actingAsSuperAdmin();
        $target = $this->createUser([
            'username' => 'verifyuser',
            'email' => 'verify@example.com',
            'status' => \App\Enums\UserStatusEnum::PENDING_VERIFICATION->value,
            'is_active' => false,
        ]);

        $response = $this->postJson("{$this->baseUrl}/users/{$target->id}/verify", [
            'status' => \App\Enums\UserStatusEnum::ACTIVE->value,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
