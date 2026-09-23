<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdatePermissionRequest;
use App\Http\Resources\Employee\PermissionResource;
use App\Http\Service\Employee\PermissionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService
    )
    {
    }

    public function update(
        UpdatePermissionRequest $request,
        string                  $employee_code
    ): JsonResponse
    {
        $this->permissionService->update(
            permissionIds: $request->validated(
                'permission_ids'
            ),
            employeeCode: $employee_code,
            actor: $request->user()
        );

        return ApiResponse::success(
            message: 'Permission pegawai berhasil diperbarui'
        );
    }

    public function index(
        Request $request,
        string  $employeeCode
    ): JsonResponse
    {
        $permissions = $this->permissionService->getForEmployee(
            employeeCode: $employeeCode,
            actor: $request->user(),
        );

        $data = PermissionResource::collection(
            $permissions
        )->resolve($request);

        return ApiResponse::success(
            message: 'Data permission berhasil diambil',
            data: $data,
        );
    }
}
