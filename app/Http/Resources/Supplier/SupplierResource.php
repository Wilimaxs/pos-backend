<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'supplier_code' => $this->supplier_code,
            'name' => $this->name,
            'person_responsible' => $this->person_responsible,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];
    }
}
