<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $employee = $this->resource['employee'];

        return [
            'token' => $this->resource['token'],
            'employee' => [
                'employee_code' => $employee->employee_code,
                'store_code' => $employee->store_code,
                'store_name' => $employee->store?->name,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'is_owner' => $employee->is_owner,
            ],
            'permissions' => $employee->employeePermissions->pluck('permission.name')->all(),
        ];
    }
}
