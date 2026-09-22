<?php

namespace App\Http\Service\Employee;

use App\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
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
}
