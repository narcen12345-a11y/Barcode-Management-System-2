<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    private const SITES = [
        ['SITE-001', 'Jakarta Pusat', 'Jakarta', 'Jl. Merdeka No. 1, Jakarta Pusat', '-6.1818', '106.8223'],
        ['SITE-002', 'Surabaya Timur', 'Jawa Timur', 'Jl. Raya Manyar No. 45, Surabaya', '-7.2575', '112.7521'],
        ['SITE-003', 'Bandung Utara', 'Jawa Barat', 'Jl. Setiabudi No. 88, Bandung', '-6.8911', '107.6103'],
        ['SITE-004', 'Medan Kota', 'Sumatera Utara', 'Jl. Balai Kota No. 12, Medan', '3.5952', '98.6722'],
        ['SITE-005', 'Makassar Barat', 'Sulawesi Selatan', 'Jl. Ujung Pandang No. 33, Makassar', '-5.1477', '119.4327'],
    ];

    public function run(): void
    {
        foreach (self::SITES as [$siteId, $siteName, $region, $address, $lat, $lng]) {
            Site::firstOrCreate(
                ['site_id' => $siteId],
                [
                    'site_name' => $siteName,
                    'region' => $region,
                    'address' => $address,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'is_active' => true,
                ]
            );
        }
    }
}
