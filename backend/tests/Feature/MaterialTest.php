<?php

namespace Tests\Feature;

use Tests\TestCase;

class MaterialTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_materials()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/materials");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_materials()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/materials");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_all_materials_unpaginated()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/materials/all");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_material()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::first();

        $response = $this->postJson("{$this->baseUrl}/materials", [
            'code' => 'TEST-MAT',
            'name' => 'Test Material',
            'material_type_id' => $materialType->id,
            'material_model_id' => $materialModel->id,
            'unit' => 'pcs',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('materials', ['code' => 'TEST-MAT']);
    }

    /** @test */
    public function it_validates_create_material_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/materials", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name', 'material_type_id', 'material_model_id', 'unit']);
    }

    /** @test */
    public function it_can_show_material()
    {
        $this->actingAsSuperAdmin();
        $material = \App\Models\Material::first();

        $response = $this->getJson("{$this->baseUrl}/materials/{$material->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_material()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::first();
        $material = \App\Models\Material::create([
            'code' => 'UPD-MAT',
            'name' => 'Updatable Material',
            'material_type_id' => $materialType->id,
            'material_model_id' => $materialModel->id,
            'unit' => 'pcs',
        ]);

        $response = $this->putJson("{$this->baseUrl}/materials/{$material->id}", [
            'code' => 'UPDATED-MAT',
            'name' => 'Updated Material',
            'material_type_id' => $materialType->id,
            'material_model_id' => $materialModel->id,
            'unit' => 'box',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('materials', ['code' => 'UPDATED-MAT']);
    }

    /** @test */
    public function it_can_delete_material()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::first();
        $material = \App\Models\Material::create([
            'code' => 'DEL-MAT',
            'name' => 'Deletable Material',
            'material_type_id' => $materialType->id,
            'material_model_id' => $materialModel->id,
            'unit' => 'pcs',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/materials/{$material->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('materials', ['id' => $material->id]);
    }

    /** @test */
    public function it_can_restore_material()
    {
        $this->actingAsSuperAdmin();
        $materialType = \App\Models\MaterialType::first();
        $materialModel = \App\Models\MaterialModel::first();
        $material = \App\Models\Material::create([
            'code' => 'REST-MAT',
            'name' => 'Restorable Material',
            'material_type_id' => $materialType->id,
            'material_model_id' => $materialModel->id,
            'unit' => 'pcs',
        ]);
        $material->delete();

        $response = $this->postJson("{$this->baseUrl}/materials/{$material->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'deleted_at' => null,
        ]);
    }
}
