<?php

namespace App\Http\Service\Product;

use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function paginate(
        array    $filter,
        Employee $actor,
    ): LengthAwarePaginator
    {
        if (!$actor->is_owner && isset($filter['store_code'])
            && $filter['store_code'] !== $actor->store_code) {
            throw new AuthorizationException;
        }

        return Product::query()
            ->select([
                'products.sku', 'products.category_code', 'products.barcode',
                'products.name', 'products.unit', 'products.cost_price',
                'products.description', 'products.is_active',
            ])
            ->with([
                'category:category_code,name',
                'productStocks' => function ($query) use ($actor, $filter): void {
                    $query->select([
                        'id', 'sku', 'store_code', 'stock_minimum',
                        'stock_quantity', 'selling_price',
                    ])->orderBy('store_code');

                    if ($actor->is_owner) {
                        $query->with('store:store_code,name,phone,is_active');
                    }

                    if (!$actor->is_owner || isset($filter['store_code'])) {
                        $query->where('store_code', $filter['store_code'] ?? $actor->store_code);
                    }
                },
            ])
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('sku', 'like', "%$search%")
                            ->orWhere('name', 'like', "%$search%")
                            ->orWhere('barcode', 'like', "%$search%");
                    });
                })
            ->when(
                filled($filter['category_code'] ?? null),
                function ($query) use ($filter): void {
                    $query
                        ->where('category_code', $filter['category_code']);
                })
            ->when(
                isset($filter['is_active']),
                function ($query) use ($filter): void {
                    $query->where('is_active', $filter['is_active']);
                })
            ->orderBy('name')
            ->orderBy('sku')
            ->paginate(20);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data): Product {

            $productData = [
                'category_code' => $data['category_code'],
                'barcode' => $data['barcode'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'unit' => $data['unit'],
                'is_active' => $data['is_active'],
            ];

            $product = Product::query()->create($productData);

            $now = now();
            Store::query()
                ->select('store_code')
                ->chunkById(
                    500,
                    function ($stores) use ($product, $data, $now): void {
                        $rows = [];
                        foreach ($stores as $store) {
                            $rows[] = [
                                'sku' => $product->sku,
                                'store_code' => $store->store_code,
                                'stock_minimum' => $data['stock_minimum'] ?? 0,
                                'stock_quantity' => 0,
                                'selling_price' => $data['selling_price'] ?? 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        ProductStock::query()->insert($rows);
                    }, 'store_code'
                );

            return $product;
        });
    }

    public function update(string $sku, array $data, Employee $actor): void
    {
        $master = array_intersect_key($data, array_flip([
            'category_code', 'barcode', 'name', 'description', 'unit', 'is_active',
        ]));

        $hasMinimum = array_key_exists('stock_minimum', $data);
        $hasPrice = array_key_exists('selling_price', $data);

        $canManage = $actor->hasPermission('product.manage');

        if (($master || $hasMinimum) && !$canManage) {
            throw new AuthorizationException;
        }

        if ($hasPrice && !$canManage && !$actor->hasPermission('product.manage-price')) {
            throw new AuthorizationException;
        }

        if (!$actor->is_owner && isset($data['store_code'])
            && $data['store_code'] !== $actor->store_code) {
            throw new AuthorizationException;
        }

        DB::transaction(function () use ($sku, $data, $master, $hasMinimum, $hasPrice, $actor): void {
            $product = Product::query()
                ->whereKey($sku)
                ->firstOrFail();

            if ($master) {
                $product->update($master);
            }

            if ($hasMinimum || $hasPrice) {
                $storeCode = $actor->is_owner ? $data['store_code'] : $actor->store_code;
                $stock = ProductStock::query()
                    ->where('sku', $sku)
                    ->where('store_code', $storeCode)
                    ->firstOrFail();

                if ($hasMinimum) {
                    $stock->stock_minimum = $data['stock_minimum'];
                }

                if ($hasPrice) {
                    $stock->selling_price = $data['selling_price'];
                }

                $stock->save();
            }
        });
    }
}
