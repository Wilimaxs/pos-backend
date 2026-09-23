<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $store = $this->store;

        return [
            'employee_code' => $this->employee_code,
            'store' => $store ? [
                'code' => $store->store_code,
                'name' => $store->name,
                'address' => $store->address,
                'phone' => $store->phone,
                'type' => $store->type,
            ] : null,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ];
    }
}
