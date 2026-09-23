<?php

namespace App\Http\Service\Employee;

use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function update(
        array    $permissionIds,
        string   $employeeCode,
        Employee $actor
    ): void
    {
        $employee = Employee::query()
            ->where('employees.employee_code', $employeeCode)
            ->firstOrFail();

        if (!$actor->hasPermission('employee.manage-permission-all')
            && $employee->store_code !== $actor->store_code
        ) {
            throw new AuthorizationException;
        }

        if ($employee->is_owner && !$actor->is_owner) {
            throw new AuthorizationException;
        }

        if (! $actor->is_owner) {
            $requestedOwnerOnly = Permission::query()
                ->whereIn('id', $permissionIds)
                ->where('is_owner_only', true)
                ->exists();

            $targetHasOwnerOnly = $employee
                ->permissions()
                ->where('permissions.is_owner_only', true)
                ->exists();

            if ($requestedOwnerOnly || $targetHasOwnerOnly) {
                throw new AuthorizationException;
            }
        }

        DB::transaction(
            function () use ($employee, $permissionIds) {
                $employee->permissions()->sync($permissionIds);
            }
        );
    }
}
