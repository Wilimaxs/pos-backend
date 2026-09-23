<?php

namespace Database\Seeders;

use App\Enum\StoreType;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::query()->updateOrCreate(
            ['email' => 'pusat@pos.test'],
            [
                'name' => 'Toko Pusat',
                'phone' => '0262000001',
                'address' => 'Garut',
                'type' => StoreType::CENTRAL,
                'is_active' => true,
            ],
        );

        Store::query()->updateOrCreate(
            ['email' => 'cabang.garut@pos.test'],
            [
                'name' => 'Cabang Garut',
                'phone' => '0262000002',
                'address' => 'Garut',
                'type' => StoreType::BRANCH,
                'is_active' => true,
            ],
        );
    }
}
