<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Service\Employee\EmployeeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    )
    {
    }

    public function create(CreateEmployeeRequest $request): JsonResponse
    {
        $this->employeeService->create(
            $request->validated()
        );

        return ApiResponse::success(
            message: 'Data Pegawai berhasil dibuat',
            statusCode: 201
        );
    }

    public function update(
        UpdateEmployeeRequest $request,
        string $employeeCode,
    ): JsonResponse
    {
        $this->employeeService->update(
            data: $request->validated(),
            employeeCode: $employeeCode,
            actor: $request->user(),
        );

        return ApiResponse::success(
            message: 'Data Pegawai berhasil diupdate'
        );
    }
}
