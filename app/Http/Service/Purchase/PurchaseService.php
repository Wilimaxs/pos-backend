<?php

namespace App\Http\Service\Purchase;

use App\Enum\StoreType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\Store;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function paginate(array $filter, Employee $actor): LengthAwarePaginator
    {
        if (!$actor->is_owner && isset($filter['store_code'])
            && $filter['store_code'] !== $actor->store_code) {
            throw new AuthorizationException;
        }

        return Purchase::query()
            ->select([
                'purchases.purchase_code',
                'purchases.employee_code', 'purchases.purchase_date',
                'purchases.payment_method', 'purchases.created_at',
            ])
            ->with([
                'employee:employee_code,name',
            ])
            ->withCount('items')
            ->when(
                !$actor->is_owner,
                function ($query) use ($actor) {
                    $query->where('store_code', $actor->store_code);
                }
            )
            ->when(
                $actor->is_owner && filled($filter['store_code'] ?? null),
                function ($query) use ($filter) {
                    $query->where('store_code', $filter['store_code']);
                }
            )
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter) {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('purchases.purchase_code', 'like', "%$search%")
                            ->orWhereHas(
                                'supplier',
                                function ($supplierQuery) use ($search) {
                                    $supplierQuery
                                        ->where('name', 'like', "%$search%");
                                });
                    });
                }
            )
            ->orderByDesc('created_at')
            ->orderByDesc('purchase_code')
            ->paginate(20);
    }

    public function detail(string $purchaseCode, Employee $actor): Purchase
    {
        return Purchase::query()
            ->with([
                'store:store_code,name',
                'supplier:supplier_code,name',
                'employee:employee_code,name',
                'items:id,purchase_code,sku,quantity,unit_price',
                'items.product:sku,name,unit',
            ])
            ->when(
                !$actor->is_owner,
                function ($query) use ($actor) {
                    $query->where('store_code', $actor->store_code);
                }
            )
            ->whereKey($purchaseCode)
            ->firstOrFail();
    }

    public function create(array $data, Employee $actor): Purchase
    {
        $storeCode = $actor->is_owner ? $data['store_code'] : $actor->store_code;

        if (!$actor->is_owner && isset($data['store_code'])
            && $data['store_code'] !== $storeCode) {
            throw new AuthorizationException;
        }

        $store = Store::query()->whereKey($storeCode)->first();

        if (!$store || !$store->is_active || $store->type !== StoreType::CENTRAL) {
            throw ValidationException::withMessages([
                'store_code' => ['Pembelian hanya dapat dicatat pada toko central yang aktif.'],
            ]);
        }

        // Combine items with the same SKU
        $bySku = [];
        foreach ($data['items'] as $item) {
            $sku = $item['sku'];
            $bySku[$sku]['quantity'] = ($bySku[$sku]['quantity'] ?? 0) + $item['quantity'];
            $bySku[$sku]['amount'] = ($bySku[$sku]['amount'] ?? 0) + $item['quantity'] * $item['unit_price'];
        }
        // Sort by SKU
        ksort($bySku);

        try {
            return DB::transaction(
                function () use ($data, $actor, $storeCode, $bySku): Purchase {
                    // Create purchase record
                    $purchase = Purchase::query()->create([
                        'supplier_receipt_number' => $data['supplier_receipt_number'],
                        'supplier_code' => $data['supplier_code'] ?? null,
                        'store_code' => $storeCode,
                        'employee_code' => $actor->employee_code,
                        'purchase_date' => $data['purchase_date'],
                        'payment_method' => $data['payment_method'],
                    ]);

                    foreach ($bySku as $sku => $value) {
                        // Find product by SKU And lock for update
                        $product = Product::query()
                            ->whereKey($sku)
                            ->lockForUpdate()
                            ->firstOrFail();

                        // Calculate quantity from all store branch and central
                        $quantityBefore = (float)ProductStock::query()
                            ->where('sku', $sku)
                            ->sum('stock_quantity');

                        // Take Stock from store who make the purchase
                        $stock = ProductStock::query()
                            ->where('sku', $sku)
                            ->where('store_code', $storeCode)
                            ->lockForUpdate()
                            ->firstOrFail();

                        // Calculate quantity after purchase
                        $quantityAfter = $quantityBefore + $value['quantity'];

                        $averageCost = (
                                ($quantityBefore * (float)$product->cost_price) +
                                $value['amount']
                            ) / $quantityAfter;

                        // save new average cost to Product table
                        $product->cost_price = number_format($averageCost, 4, '.', '');
                        $product->save();

                        // update stock quantity
                        $stock->stock_quantity += $value['quantity'];
                        $stock->save();
                    }

                    // save purchase items to PurchaseItem table
                    foreach ($data['items'] as $item) {
                        $purchase->items()->create($item);
                    }

                    return $purchase;
                });

        } catch (UniqueConstraintViolationException $exception) {
            // check if supplier receipt number already exists
            if (Purchase::query()
                ->where('supplier_receipt_number', $data['supplier_receipt_number'])
                ->exists()) {
                throw ValidationException::withMessages([
                    'supplier_receipt_number' => ['Nomor struk pemasok sudah tercatat.'],
                ]);
            }

            throw $exception;
        }
    }
}
