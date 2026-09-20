<?php

namespace App\Http\Service\Supplier;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class SupplierService
{
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Supplier::query()
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('suppliers.supplier_code', 'like', "%$search%")
                            ->orWhere('suppliers.name', 'like', "%$search%");
                    });
                }
            )
            ->when(
                array_key_exists('is_active', $filter),
                function ($query) use ($filter): void {
                    $query->where('suppliers.is_active', $filter['is_active']);
                }
            )
            ->orderByDesc('suppliers.created_at')
            ->orderBy('suppliers.name')
            ->paginate(20);
    }

    public function options(array $filter): LengthAwarePaginator
    {
        return Supplier::query()
            ->select([
                'suppliers.supplier_code',
                'suppliers.name',
                'suppliers.phone',
            ])
            ->where('suppliers.is_active', true)
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('suppliers.supplier_code', 'like', "%$search%")
                            ->orWhere('suppliers.name', 'like', "%$search%");
                    });
                }
            )
            ->orderBy('suppliers.name')
            ->paginate(20);
    }

    public function create(
        array $data
    ): Supplier
    {
        $exists = Supplier::query()
            ->where('suppliers.name', $data['name'])
            ->where('suppliers.phone', $data['phone'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'phone' => [
                    'Supplier dengan nama dan nomor telepon tersebut sudah terdaftar.',
                ],
            ]);
        }

        return Supplier::query()
            ->create($data);
    }

    public function update(
        string $supplierCode,
        array  $data,
    ): void
    {
        $supplier = Supplier::query()
            ->whereKey($supplierCode)
            ->firstOrFail();

        $name = $data['name'] ?? $supplier->name;
        $phone = $data['phone'] ?? $supplier->phone;

        $exists = Supplier::query()
            ->where('suppliers.name', $name)
            ->where('suppliers.phone', $phone)
            ->where('suppliers.supplier_code', '!=', $supplierCode)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'phone' => [
                    'Supplier dengan nama dan nomor telepon tersebut sudah terdaftar.',
                ],
            ]);
        }

        $supplier->update($data);
    }
}
