<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserStatusEnum;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $this->createUser([
            'username' => 'logintest',
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson("{$this->baseUrl}/login", [
            'login' => 'logintest',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'token'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_login_with_email()
    {
        $this->createUser([
            'username' => 'loginemail',
            'email' => 'loginemail@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson("{$this->baseUrl}/login", [
            'login' => 'loginemail@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson("{$this->baseUrl}/login", [
            'login' => 'nonexistent',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Kredensial tidak valid.',
            ]);
    }

    /** @test */
    public function it_cannot_login_with_inactive_user()
    {
        $this->createUser([
            'username' => 'inactiveuser',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
            'status' => UserStatusEnum::INACTIVE->value,
        ]);

        $response = $this->postJson("{$this->baseUrl}/login", [
            'login' => 'inactiveuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_login_and_password()
    {
        $response = $this->postJson("{$this->baseUrl}/login", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }

    /** @test */
    public function it_can_logout()
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/logout");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/me");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'username', 'email', 'full_name', 'roles'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_authentication_for_me()
    {
        $response = $this->getJson("{$this->baseUrl}/me");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authentication_for_logout()
    {
        $response = $this->postJson("{$this->baseUrl}/logout");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_change_password()
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/change-password", [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_cannot_change_password_with_wrong_current_password()
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/change-password", [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_validates_change_password_request()
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/change-password", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password', 'new_password']);
    }
}
