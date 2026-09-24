<?php

namespace App\Http\Service\Store;

use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoreService
{
    public function paginate(
        array $filter,
        Employee $actor,
    ): LengthAwarePaginator
    {
        $query = Store::query();

        if (! $actor->is_owner) {
            $query->where('stores.store_code', $actor->store_code);
        }

        return $query
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

    public function options(
        array $filter,
        Employee $actor,
    ): LengthAwarePaginator
    {
        $query = Store::query();

        if (! $actor->is_owner) {
            $query->where('stores.store_code', $actor->store_code);
        }

        return $query
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
        return DB::transaction(function () use ($data): Store {
            $store = Store::query()->create($data);

            $now = now();
            Product::query()->select('sku')->chunkById(500, function ($products) use ($store, $now): void {
                $rows = [];
                foreach ($products as $product) {
                    $rows[] = [
                        'sku' => $product->sku,
                        'store_code' => $store->store_code,
                        'stock_minimum' => 0,
                        'stock_quantity' => 0,
                        'selling_price' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                ProductStock::query()->insert($rows);
            }, 'sku');

            return $store;
        });
    }

    public function update(
        string $storeCode,
        array  $data,
        Employee $actor,
    ): void
    {
        $store = Store::query()
            ->whereKey($storeCode)
            ->firstOrFail();

        if (! $actor->is_owner && $store->store_code !== $actor->store_code) {
            throw new AuthorizationException;
        }

        $store->update($data);
    }
}
