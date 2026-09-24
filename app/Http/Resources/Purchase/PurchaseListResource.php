<?php

namespace App\Http\Resources\Purchase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'purchase_code' => $this->purchase_code,
            'purchase_date' => $this->purchase_date->format('Y-m-d'),
            'employee_name' => $this->employee?->name,
            'payment_method' => $this->payment_method,
            'items_count' => $this->items_count,
            'created_at' => $this->created_at,
        ];
    }
}
