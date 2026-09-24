<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StoreSeeder::class,
            PermissionSeeder::class,
            EmployeeSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
