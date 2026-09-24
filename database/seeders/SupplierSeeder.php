<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distributor Minuman Garut',
                'person_responsible' => 'Andi',
                'phone' => '0262000011',
                'address' => 'Garut, Jawa Barat',
                'email' => 'minuman@pos.test',
            ],
            [
                'name' => 'Distributor Makanan Ringan Garut',
                'person_responsible' => 'Budi',
                'phone' => '0262000012',
                'address' => 'Garut, Jawa Barat',
                'email' => 'makanan@pos.test',
            ],
            [
                'name' => 'Distributor Kebutuhan Harian Garut',
                'person_responsible' => 'Citra',
                'phone' => '0262000013',
                'address' => 'Garut, Jawa Barat',
                'email' => 'harian@pos.test',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::query()->firstOrCreate(
                [
                    'name' => $supplier['name'],
                    'phone' => $supplier['phone'],
                ],
                [
                    'person_responsible' => $supplier['person_responsible'],
                    'address' => $supplier['address'],
                    'email' => $supplier['email'],
                    'is_active' => true,
                ],
            );
        }
    }
}
