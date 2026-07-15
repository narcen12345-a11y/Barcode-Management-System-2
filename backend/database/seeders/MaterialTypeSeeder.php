<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    private const TYPES = [
        ['Cable', 'Electrical cables and wiring'],
        ['Pipe', 'Piping and tubing materials'],
        ['Valve', 'Valves and flow control devices'],
        ['Fitting', 'Pipe fittings and connectors'],
        ['Instrument', 'Measurement and control instruments'],
        ['Electrical', 'Electrical components and devices'],
        ['Mechanical', 'Mechanical parts and assemblies'],
        ['Safety', 'Safety equipment and supplies'],
        ['Structural', 'Structural steel and supports'],
        ['Consumable', 'Consumable materials and supplies'],
    ];

    public function run(): void
    {
        foreach (self::TYPES as [$name, $description]) {
            MaterialType::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'is_active' => true,
                ]
            );
        }
    }
}
