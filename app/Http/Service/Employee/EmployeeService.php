<?php

namespace App\Http\Service\Employee;

use App\Models\Employee;

class EmployeeService
{
    public function createEmployee(array $data): Employee
    {
        return Employee::query()->create($data);
    }
}
