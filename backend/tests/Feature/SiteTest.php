<?php

namespace Tests\Feature;

use Tests\TestCase;

class SiteTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_sites()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/sites");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_sites()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/sites");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_sites_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/sites/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_site()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/sites", [
            'code' => 'TEST-SITE',
            'name' => 'Test Site',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'province' => 'Test Province',
            'phone' => '02112345678',
            'email' => 'site@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('sites', ['code' => 'TEST-SITE']);
    }

    /** @test */
    public function it_validates_create_site_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/sites", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name']);
    }

    /** @test */
    public function it_can_show_site()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();

        $response = $this->getJson("{$this->baseUrl}/sites/{$site->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_site()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::create([
            'code' => 'UPD-SITE',
            'name' => 'Updatable Site',
        ]);

        $response = $this->putJson("{$this->baseUrl}/sites/{$site->id}", [
            'code' => 'UPDATED-SITE',
            'name' => 'Updated Site',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('sites', ['code' => 'UPDATED-SITE']);
    }

    /** @test */
    public function it_can_delete_site()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::create([
            'code' => 'DEL-SITE',
            'name' => 'Deletable Site',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/sites/{$site->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
    }

    /** @test */
    public function it_can_restore_site()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::create([
            'code' => 'REST-SITE',
            'name' => 'Restorable Site',
        ]);
        $site->delete();

        $response = $this->postJson("{$this->baseUrl}/sites/{$site->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'deleted_at' => null,
        ]);
    }
}
