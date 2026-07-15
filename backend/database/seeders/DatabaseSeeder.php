<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SiteSeeder::class,
            MaterialTypeSeeder::class,
            MaterialModelSeeder::class,
            MaterialSeeder::class,
            BarcodeSeeder::class,
        ]);
    }
}
