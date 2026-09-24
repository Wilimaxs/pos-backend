<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sku' => $this->sku,
            'category_code' => $this->category_code,
            'category_name' => $this->category?->name,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'unit' => $this->unit,
            'cost_price' => $this->when(
                $request->user()?->is_owner,
                fn() => (int)$this->cost_price
            ),
            'description' => $this->description,
            'is_active' => $this->is_active,
            'product_stocks' => $this->productStocks
                ->map(function ($stock) use ($request): array {
                    $data = [
                        'store_code' => $stock->store_code,
                        'stock_minimum' => (int)$stock->stock_minimum,
                        'stock_quantity' => (int)$stock->stock_quantity,
                        'selling_price' => (int)$stock->selling_price,
                    ];
                    if ($request->user()?->is_owner) {
                        $data['store'] = [
                            'name' => $stock->store?->name,
                            'phone' => $stock->store?->phone,
                            'is_active' => $stock->store?->is_active,
                        ];
                    }

                    return $data;
                })->all(),
        ];
    }
}
