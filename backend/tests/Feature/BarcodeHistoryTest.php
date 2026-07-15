<?php

namespace Tests\Feature;

use Tests\TestCase;

class BarcodeHistoryTest extends TestCase
{
    private string $baseUrl = '/api';

    /** @test */
    public function it_can_list_barcode_history()
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson("{$this->baseUrl}/barcode-history");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_requires_permission_to_list_barcode_history()
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("{$this->baseUrl}/barcode-history");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_show_barcode_history_detail()
    {
        $this->actingAsSuperAdmin();
        $history = \App\Models\BarcodeHistory::first();

        if (!$history) {
            $site = \App\Models\Site::first();
            $material = \App\Models\Material::first();
            $barcode = \App\Models\Barcode::create([
                'site_id' => $site->id,
                'material_id' => $material->id,
                'barcode_number' => 'HIST-DTL-001',
                'quantity' => 10,
                'status' => \App\Enums\BarcodeStatusEnum::ACTIVE->value,
                'production_date' => '2026-07-15',
            ]);
            $history = \App\Models\BarcodeHistory::create([
                'barcode_id' => $barcode->id,
                'user_id' => $this->createSuperAdmin()->id,
                'type' => \App\Enums\BarcodeHistoryTypeEnum::CREATED->value,
                'description' => 'Barcode created',
            ]);
        }

        $response = $this->getJson("{$this->baseUrl}/barcode-history/{$history->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'type', 'description'],
            ])
            ->assertJson(['success' => true]);
    }
}
