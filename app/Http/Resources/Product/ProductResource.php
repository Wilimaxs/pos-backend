<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stock = $this->productStocks->first();

        return [
            'sku' => $this->sku,
            'category_code' => $this->category_code,
            'category_name' => $this->category?->name,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'unit' => $this->unit,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'selling_price' => (int) ($stock?->selling_price ?? 0),
            'stock_quantity' => (int) ($stock?->stock_quantity ?? 0),
            'stock_minimum' => (int) ($stock?->stock_minimum ?? 0),
        ];
    }
}
