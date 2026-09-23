<?php

namespace App\Http\Service\Employee;

use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
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

        if (! $actor->is_owner && $employee->store_code !== $actor->store_code) {
            throw new AuthorizationException;
        }

        if ($employee->is_owner && !$actor->is_owner) {
            throw new AuthorizationException;
        }

        if (!$actor->is_owner) {
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

    public function getForEmployee(
        string   $employeeCode,
        Employee $actor
    ): Collection
    {

        $employee = Employee::query()
            ->where('employees.employee_code', $employeeCode)
            ->firstOrFail();

        if (!$actor->is_owner && $employee->store_code !== $actor->store_code) {
            throw new AuthorizationException;
        }

        if ($employee->is_owner && !$actor->is_owner) {
            throw new AuthorizationException;
        }

        if (!$actor->is_owner) {
            $targetHasOwnerOnly = $employee
                ->permissions()
                ->where('permissions.is_owner_only', true)
                ->exists();

            if ($targetHasOwnerOnly) {
                throw new AuthorizationException;
            }
        }

        $assignedPermissionIds = $employee->employeePermissions()->pluck('permission_id');

        $query = Permission::query()
            ->select([
                'id',
                'name',
            ]);

        if (!$actor->is_owner) {
            $query->where('is_owner_only', false);
        }

        $permissions = $query
            ->orderBy('name')
            ->get();

        $permissions->each(
            function (Permission $permission) use (
                $employee,
                $assignedPermissionIds
            ): void {
                $permission->setAttribute(
                    'is_assigned',
                    $employee->is_owner || $assignedPermissionIds->contains($permission->id)
                );
            }
        );

        return $permissions;
    }
}
