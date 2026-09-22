<?php

namespace App\Http\Service\Employee;

use App\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    public function create(
        array $data,
    ): void
    {
        Employee::query()->create($data);
    }

    public function update(array $data, string $employeeCode, Employee $actor): void
    {
        $employee = Employee::query()
            ->where('employees.employee_code', $employeeCode)
            ->firstOrFail();

        if ($employee->is_owner && !$actor->is_owner) {
            throw new AuthorizationException;
        }

        if ($employee->is_owner && $actor->is_owner &&
            array_key_exists('is_active', $data) && $data['is_active'] === false) {
            throw ValidationException::withMessages([
                'is_active' => [
                    'Anda tidak dapat menonaktifkan akun sendiri.',
                ],
            ]);
        }

        $employee->update($data);
    }

    public function pagination(
        array $filters
    ): LengthAwarePaginator
    {
        return Employee::query()
            ->with([
                'store:store_code,name,address,phone,type'
            ])
            ->when(
                filled($filters['search'] ?? null),
                function ($query) use ($filters) {
                    $search = trim($filters['search']);
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('employees.employee_code', 'like', "%$search%")
                            ->orWhere('employees.name', 'like', "%$search%");
                    });
                }
            )
            ->when(
                array_key_exists('is_active', $filters),
                function ($query) use ($filters) {
                    $query->where('employees.is_active', $filters['is_active']);
                }
            )
            ->when(
                filled($filters['store_code'] ?? null),
                function ($query) use ($filters) {
                    $query->where('employees.store_code', $filters['store_code']);
                }
            )
            ->orderByDesc('employees.created_at')
            ->orderBy('employees.name')
            ->paginate(20);
    }
}
