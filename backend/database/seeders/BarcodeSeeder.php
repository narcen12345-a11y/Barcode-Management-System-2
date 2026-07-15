<?php

namespace Database\Seeders;

use App\Models\Barcode;
use App\Models\Material;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;

class BarcodeSeeder extends Seeder
{
    /**
     * Generate 300 barcodes distributed across materials and sites.
     * Each barcode gets a unique barcode_id (BRC-XXXXX) and serial_number.
     */
    public function run(): void
    {
        $materialIds = Material::pluck('id')->toArray();
        $siteIds = Site::pluck('id')->toArray();
        $adminUser = User::where('username', 'admin')->first();

        if (empty($materialIds) || empty($siteIds) || !$adminUser) {
            return;
        }

        $statuses = ['NEW', 'OLD'];
        $barcodes = [];
        $now = now();

        for ($i = 1; $i <= 300; $i++) {
            $barcodeId = 'BRC-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT);
            $serialNumber = 'SN-' . strtoupper(dechex(100000 + $i));

            $barcodes[] = [
                'barcode_id' => $barcodeId,
                'material_id' => $materialIds[array_rand($materialIds)],
                'site_id' => $siteIds[array_rand($siteIds)],
                'serial_number' => $serialNumber,
                'status' => $statuses[array_rand($statuses)],
                'description' => "Barcode {$barcodeId} for material tracking",
                'is_active' => true,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($barcodes as $barcode) {
            Barcode::firstOrCreate(
                ['barcode_id' => $barcode['barcode_id']],
                $barcode
            );
        }
    }
}
