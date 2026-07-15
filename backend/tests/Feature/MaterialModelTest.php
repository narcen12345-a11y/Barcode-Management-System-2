<?php

namespace Tests\Feature;

use Tests\TestCase;

class MaterialModelTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_material_models()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/material-models");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_material_models()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/material-models");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_material_models_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/material-models/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_material_model()
    {
        $materialType = \App\Models\MaterialType::first();

        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/material-models", [
            'code' => 'TEST-MM',
            'name' => 'Test Material Model',
            'material_type_id' => $materialType->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_models', ['code' => 'TEST-MM']);
    }

    /** @test */
    public function it_validates_create_material_model_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/material-models", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name', 'material_type_id']);
    }

    /** @test */
    public function it_can_show_material_model()
    {
        $this->actingAsSuperAdmin();
        $materialModel = \App\Models\MaterialModel::first();

        $response = $this->getJson("{$this->baseUrl}/material-models/{$materialModel->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_material_model()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::create([
            'code' => 'UPD-MM',
            'name' => 'Updatable Material Model',
            'material_type_id' => $materialType->id,
        ]);

        $response = $this->putJson("{$this->baseUrl}/material-models/{$materialModel->id}", [
            'code' => 'UPDATED-MM',
            'name' => 'Updated Material Model',
            'material_type_id' => $materialType->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_models', ['code' => 'UPDATED-MM']);
    }

    /** @test */
    public function it_can_delete_material_model()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::create([
            'code' => 'DEL-MM',
            'name' => 'Deletable Material Model',
            'material_type_id' => $materialType->id,
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/material-models/{$materialModel->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('material_models', ['id' => $materialModel->id]);
    }

    /** @test */
    public function it_can_restore_material_model()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::create([
            'code' => 'REST-MM',
            'name' => 'Restorable Material Model',
            'material_type_id' => $materialType->id,
        ]);
        $materialModel->delete();

        $response = $this->postJson("{$this->baseUrl}/material-models/{$materialModel->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('material_models', [
            'id' => $materialModel->id,
            'deleted_at' => null,
        ]);
    }
}
