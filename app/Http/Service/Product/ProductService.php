<?php

namespace App\Http\Service\Product;

use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProductService
{
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Product::query()
            ->select([
                'products.sku', 'products.image_path',
                'products.category_code', 'products.name',
                'products.is_active',
            ])
            ->with([
                'category:category_code,name',
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

    public function detail(string $sku, Employee $actor): Product
    {
        return Product::query()
            ->with([
                'category:category_code,name',
                'productStocks' => function ($query) use ($actor): void {
                    $query->select([
                        'id', 'sku', 'store_code', 'stock_minimum',
                        'stock_quantity', 'selling_price',
                    ])->orderBy('store_code');

                    if ($actor->is_owner) {
                        $query->with('store:store_code,name,phone,is_active');
                    } else {
                        $query->where('store_code', $actor->store_code);
                    }
                },
            ])
            ->whereKey($sku)
            ->firstOrFail();
    }

    public function create(array $data): Product
    {
        $imagePath = null;
        if (isset($data['image'])) {
            $imagePath = $data['image']->store('products', 'public');
            if (!$imagePath) {
                throw new RuntimeException('Gambar produk gagal disimpan.');
            }
        }

        try {
            return DB::transaction(function () use ($data, $imagePath): Product {
                $productData = [
                    'category_code' => $data['category_code'],
                    'barcode' => $data['barcode'] ?? null,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'unit' => $data['unit'],
                    'is_active' => $data['is_active'],
                    'image_path' => $imagePath,
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
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }
    }

    public function update(string $sku, array $data, Employee $actor): void
    {
        $master = array_intersect_key($data, array_flip([
            'category_code', 'barcode', 'name', 'description', 'unit', 'is_active',
        ]));

        $hasMinimum = array_key_exists('stock_minimum', $data);
        $hasPrice = array_key_exists('selling_price', $data);
        $hasImage = isset($data['image']);

        $canManage = $actor->hasPermission('product.manage');

        if (($master || $hasMinimum || $hasImage) && !$canManage) {
            throw new AuthorizationException;
        }

        if ($hasPrice && !$canManage && !$actor->hasPermission('product.manage-price')) {
            throw new AuthorizationException;
        }

        if (!$actor->is_owner && isset($data['store_code'])
            && $data['store_code'] !== $actor->store_code) {
            throw new AuthorizationException;
        }

        $imagePath = null;
        if ($hasImage) {
            $imagePath = $data['image']->store('products', 'public');
            if (!$imagePath) {
                throw new RuntimeException('Gambar produk gagal disimpan.');
            }
        }

        try {
            $oldImagePath = DB::transaction(function () use ($sku, $data, $master, $hasMinimum, $hasPrice, $actor, $imagePath): ?string {
                $product = Product::query()
                    ->whereKey($sku)
                    ->firstOrFail();

                $oldImagePath = $product->image_path;

                if ($master) {
                    $product->update($master);
                }

                if ($imagePath) {
                    $product->update(['image_path' => $imagePath]);
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

                return $oldImagePath;
            });
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        if ($imagePath && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }
    }
}
