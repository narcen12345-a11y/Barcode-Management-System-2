<?php

namespace Tests\Feature;

use Tests\TestCase;

class MaterialTypeTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_material_types()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/material-types");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_material_types()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/material-types");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_material_types_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/material-types/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_material_type()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/material-types", [
            'code' => 'TEST-MT',
            'name' => 'Test Material Type',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_types', ['code' => 'TEST-MT']);
    }

    /** @test */
    public function it_validates_create_material_type_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/material-types", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name']);
    }

    /** @test */
    public function it_can_show_material_type()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();

        $response = $this->getJson("{$this->baseUrl}/material-types/{$materialType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_material_type()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::create([
            'code' => 'UPD-MT',
            'name' => 'Updatable Material Type',
        ]);

        $response = $this->putJson("{$this->baseUrl}/material-types/{$materialType->id}", [
            'code' => 'UPDATED-MT',
            'name' => 'Updated Material Type',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_types', ['code' => 'UPDATED-MT']);
    }

    /** @test */
    public function it_can_delete_material_type()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::create([
            'code' => 'DEL-MT',
            'name' => 'Deletable Material Type',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/material-types/{$materialType->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('material_types', ['id' => $materialType->id]);
    }

    /** @test */
    public function it_can_restore_material_type()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::create([
            'code' => 'REST-MT',
            'name' => 'Restorable Material Type',
        ]);
        $materialType->delete();

        $response = $this->postJson("{$this->baseUrl}/material-types/{$materialType->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_types', [
            'id' => $materialType->id,
            'deleted_at' => null,
        ]);
    }
}
