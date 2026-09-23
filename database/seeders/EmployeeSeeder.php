<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Permission;
use App\Models\Store;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centralStore = Store::query()
            ->where('email', 'pusat@pos.test')
            ->firstOrFail();

        $branchStore = Store::query()
            ->where('email', 'cabang.garut@pos.test')
            ->firstOrFail();

        $owner = Employee::query()->firstOrNew([
            'phone' => '081200000001',
        ]);

        $owner->store_code = $centralStore->store_code;
        $owner->name = 'Owner POS';
        $owner->email = 'owner@pos.test';
        $owner->address = 'Garut';
        $owner->position = 'Owner';
        $owner->is_owner = true;
        $owner->is_active = true;

        if (!$owner->exists) {
            $owner->password = 'password123';
        }

        $owner->save();

        $employee = Employee::query()->firstOrNew([
            'phone' => '081200000002',
        ]);

        $employee->store_code = $branchStore->store_code;
        $employee->name = 'Kasir Cabang';
        $employee->email = 'kasir@pos.test';
        $employee->address = 'Garut';
        $employee->position = 'Kasir';
        $employee->is_owner = false;
        $employee->is_active = true;

        if (!$employee->exists) {
            $employee->password = 'password123';
        }

        $employee->save();

        $permissionIds = Permission::query()
            ->whereIn('name', [
                'sale.create',
                'member.view',
                'member.create',
                'product.view',
                'inventory.view',
            ])
            ->pluck('id');

        $employee->permissions()->sync($permissionIds);
    }
}
