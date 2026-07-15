<?php

namespace Database\Seeders;

use App\Models\MaterialModel;
use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialModelSeeder extends Seeder
{
    /**
     * 30 material models distributed across 10 material types (3 per type).
     */
    private const MODELS = [
        // Cable (1)
        ['Cable', 'NYA', 'Single-core PVC insulated cable'],
        ['Cable', 'NYM', 'Multi-core PVC insulated cable'],
        ['Cable', 'NYFGbY', 'Armoured power cable'],
        // Pipe (2)
        ['Pipe', 'PVC Schedule 40', 'PVC pipe schedule 40 standard'],
        ['Pipe', 'PVC Schedule 80', 'PVC pipe schedule 80 heavy duty'],
        ['Pipe', 'HDPE PN10', 'HDPE pipe PN10 rating'],
        // Valve (3)
        ['Valve', 'Gate Valve', 'Rising stem gate valve'],
        ['Valve', 'Ball Valve', 'Full port ball valve'],
        ['Valve', 'Check Valve', 'Swing check valve'],
        // Fitting (4)
        ['Fitting', 'Elbow 90°', '90 degree elbow fitting'],
        ['Fitting', 'Tee Equal', 'Equal tee connector'],
        ['Fitting', 'Reducer', 'Concentric reducer'],
        // Instrument (5)
        ['Instrument', 'Pressure Gauge', 'Bourdon tube pressure gauge'],
        ['Instrument', 'Temperature Transmitter', 'RTD temperature transmitter'],
        ['Instrument', 'Flow Meter', 'Electromagnetic flow meter'],
        // Electrical (6)
        ['Electrical', 'MCB 1P', 'Single pole miniature circuit breaker'],
        ['Electrical', 'Contactor', 'AC magnetic contactor'],
        ['Electrical', 'Relay', 'Protection relay'],
        // Mechanical (7)
        ['Mechanical', 'Pump Centrifugal', 'Centrifugal water pump'],
        ['Mechanical', 'Fan Axial', 'Axial flow fan'],
        ['Mechanical', 'Compressor', 'Air compressor unit'],
        // Safety (8)
        ['Safety', 'Fire Extinguisher', 'ABC dry powder extinguisher'],
        ['Safety', 'Safety Helmet', 'Industrial safety helmet'],
        ['Safety', 'Safety Harness', 'Full body safety harness'],
        // Structural (9)
        ['Structural', 'I-Beam 200', 'I-beam profile 200mm'],
        ['Structural', 'Channel C150', 'Channel profile C150'],
        ['Structural', 'Angle L50', 'Angle profile L50x50'],
        // Consumable (10)
        ['Consumable', 'Welding Rod E6013', 'Mild steel welding electrode'],
        ['Consumable', 'Grinding Wheel', 'Abrasive grinding disc 4"'],
        ['Consumable', 'Lubricant Grease', 'Multi-purpose lithium grease'],
    ];

    public function run(): void
    {
        $types = MaterialType::pluck('id', 'name');

        foreach (self::MODELS as [$typeName, $name, $description]) {
            MaterialModel::firstOrCreate(
                ['material_type_id' => $types[$typeName], 'name' => $name],
                [
                    'description' => $description,
                    'is_active' => true,
                ]
            );
        }
    }
}
