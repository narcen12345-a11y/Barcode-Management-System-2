<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialModel;
use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Generate 100 materials distributed across types and models.
     * Each material gets a unique material_code like MAT-00001.
     */
    public function run(): void
    {
        $types = MaterialType::pluck('id', 'name');
        $models = MaterialModel::with('materialType')->get();

        $index = 1;
        foreach ($models as $model) {
            // 3-4 materials per model to reach ~100 total
            $count = ($index <= 30) ? 3 : 4;

            for ($i = 1; $i <= $count; $i++) {
                $code = 'MAT-' . str_pad((string) $index, 5, '0', STR_PAD_LEFT);
                $materialName = $model->name . ' ' . $this->getVariant($i);

                Material::firstOrCreate(
                    ['material_code' => $code],
                    [
                        'material_type_id' => $model->material_type_id,
                        'material_model_id' => $model->id,
                        'name' => $materialName,
                        'description' => "{$materialName} — {$model->materialType->name} category",
                        'is_active' => true,
                    ]
                );

                $index++;
            }
        }
    }

    private function getVariant(int $i): string
    {
        return match ($i) {
            1 => 'Standard',
            2 => 'Premium',
            3 => 'Industrial',
            4 => 'Heavy Duty',
            default => 'Standard',
        };
    }
}
