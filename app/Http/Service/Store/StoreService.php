<?php

namespace App\Http\Service\Store;

use App\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreService
{
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Store::query()
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('stores.store_code', 'like', "%$search%")
                            ->orWhere('stores.name', 'like', "%$search%",
                            );
                    });
                }
            )
            ->when(
                array_key_exists('is_active', $filter),
                function ($query) use ($filter): void {
                    $query->where('stores.is_active', $filter['is_active']);
                }
            )
            ->orderByDesc('stores.created_at')
            ->orderBy('stores.name')
            ->paginate(20);
    }

    public function options(array $filter): LengthAwarePaginator
    {
        return Store::query()
            ->select([
                'stores.store_code',
                'stores.name',
                'stores.phone',
            ])
            ->where('stores.is_active', true)
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where('stores.store_code', 'like', "%$search%")
                                ->orWhere('stores.name', 'like', "%$search%");
                        });
                }
            )
            ->orderBy('stores.name')
            ->paginate(20);
    }

    public function create(array $data): Store
    {
        return Store::query()->create($data);
    }

    public function update(
        string $storeCode,
        array  $data,
    ): void
    {
        $store = Store::query()
            ->whereKey($storeCode)
            ->firstOrFail();

        $store->update($data);
    }
}
