<?php

namespace App\Http\Resources\Purchase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items->map(function ($item): array {
            $quantity = (int) $item->quantity;
            $unitPrice = (int) $item->unit_price;

            return [
                'sku' => $item->sku,
                'product_name' => $item->product?->name,
                'unit' => $item->product?->unit,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
            ];
        });

        return [
            'purchase_code' => $this->purchase_code,
            'supplier_receipt_number' => $this->supplier_receipt_number,
            'purchase_date' => $this->purchase_date->format('Y-m-d'),
            'store_code' => $this->store_code,
            'store_name' => $this->store?->name,
            'supplier_code' => $this->supplier_code,
            'supplier_name' => $this->supplier?->name,
            'employee_code' => $this->employee_code,
            'employee_name' => $this->employee?->name,
            'payment_method' => $this->payment_method,
            'total_amount' => $items->sum('subtotal'),
            'items' => $items->all(),
            'created_at' => $this->created_at,
        ];
    }
}
