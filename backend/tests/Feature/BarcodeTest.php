<?php

namespace Tests\Feature;

use Tests\TestCase;

class BarcodeTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_barcodes()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/barcodes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_barcodes()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/barcodes");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_create_barcode()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();

        $response = $this->postJson("{$this->baseUrl}/barcodes", [
            'site_id' => $site->id,
            'material_id' => $material->id,
            'quantity' => 10,
            'production_date' => '2026-07-15',
            'notes' => 'Test barcode creation',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'barcode_number', 'status'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('barcodes', [
            'site_id' => $site->id,
            'material_id' => $material->id,
        ]);
    }

    /** @test */
    public function it_validates_create_barcode_request()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/barcodes", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['site_id', 'material_id', 'quantity']);
    }

    /** @test */
    public function it_can_show_barcode()
    {
        $this->actingAsSuperAdmin();
        $barcode = \App\Models\Barcode::first();

        if (!$barcode) {
            $site = \App\Models\Site::first();
            $material = \App\Models\Material::first();
            $barcode = \App\Models\Barcode::create([
                'site_id' => $site->id,
                'material_id' => $material->id,
                'barcode_number' => 'TEST-BARCODE-001',
                'quantity' => 10,
                'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
                'production_date' => '2026-07-15',
            ]);
        }

        $response = $this->getJson("{$this->baseUrl}/barcodes/{$barcode->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'barcode_number', 'status', 'site', 'material'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_barcode()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'UPD-BARCODE-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->putJson("{$this->baseUrl}/barcodes/{$barcode->id}", [
            'quantity' => 20,
            'notes' => 'Updated quantity',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('barcodes', [
            'id' => $barcode->id,
            'quantity' => 20,
        ]);
    }

    /** @test */
    public function it_can_delete_barcode()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'DEL-BARCODE-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->deleteJson("{$this->baseUrl}/barcodes/{$barcode->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('barcodes', ['id' => $barcode->id]);
    }

    /** @test */
    public function it_can_restore_barcode()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'REST-BARCODE-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);
        $barcode->delete();

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/restore");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('barcodes', [
            'id' => $barcode->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_get_barcode_history()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'HIST-BARCODE-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->getJson("{$this->baseUrl}/barcodes/{$barcode->id}/history");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_generate_barcode_number()
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson("{$this->baseUrl}/barcodes/generate-number", [
            'site_id' => \App\Models\Site::first()->id,
            'material_id' => \App\Models\Material::first()->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['barcode_number'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_print_barcode()
    {
        $this->actingAsSuperAdmin();
        $barcode = \App\Models\Barcode::first();

        if (!$barcode) {
            $site = \App\Models\Site::first();
            $material = \App\Models\Material::first();
            $barcode = \App\Models\Barcode::create([
                'site_id' => $site->id,
                'material_id' => $material->id,
                'barcode_number' => 'PRINT-BARCODE-001',
                'quantity' => 10,
                'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
                'production_date' => '2026-07-15',
            ]);
        }

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/print");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_mark_barcode_as_printed()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'MARKPRINT-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/mark-printed");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_mark_barcode_as_used()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'MARKUSED-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/mark-used");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_mark_barcode_as_damaged()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'MARKDMG-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/mark-damaged", [
            'reason' => 'Test damage reason',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_mark_barcode_as_lost()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'MARKLOST-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/mark-lost", [
            'reason' => 'Test lost reason',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_mark_barcode_as_scrapped()
    {
        $this->actingAsSuperAdmin();
        $site = \App\Models\Site::first();
        $material = \App\Models\Material::first();
        $barcode = \App\Models\Barcode::create([
            'site_id' => $site->id,
            'material_id' => $material->id,
            'barcode_number' => 'MARKSCRP-001',
            'quantity' => 10,
            'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
            'production_date' => '2026-07-15',
        ]);

        $response = $this->postJson("{$this->baseUrl}/barcodes/{$barcode->id}/mark-scrapped", [
            'reason' => 'Test scrapped reason',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
