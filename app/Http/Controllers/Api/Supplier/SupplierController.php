<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\CreateSupplierRequest;
use App\Http\Requests\Supplier\SupplierOptionRequest;
use App\Http\Requests\Supplier\SupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\Supplier\SupplierOptionResource;
use App\Http\Resources\Supplier\SupplierResource;
use App\Http\Service\Supplier\SupplierService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $supplierService,
    )
    {
    }

    public function supplierList(SupplierRequest $request): JsonResponse
    {
        $suppliers = $this->supplierService->paginate(
            filter: $request->validated(),
        );

        $data = SupplierResource::collection(
            $suppliers->getCollection(),
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar Supplier berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $suppliers->currentPage(),
                'per_page' => $suppliers->perPage(),
                'total_page' => $suppliers->lastPage(),
                'total_data' => $suppliers->total(),
            ]
        );
    }

    public function options(
        SupplierOptionRequest $request,
    ): JsonResponse
    {
        $suppliers = $this->supplierService->options(
            filter: $request->validated(),
        );

        $data = SupplierOptionResource::collection(
            $suppliers->getCollection(),
        )->resolve($request);

        return ApiResponse::success(
            message: 'Pilihan Supplier berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $suppliers->currentPage(),
                'per_page' => $suppliers->perPage(),
                'total_page' => $suppliers->lastPage(),
                'total_data' => $suppliers->total(),
            ]
        );
    }

    public function create(
        CreateSupplierRequest $request,
    ): JsonResponse
    {
        $this->supplierService->create(
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Supplier berhasil dibuat',
            statusCode: 201,
        );
    }

    public function update(
        UpdateSupplierRequest $request,
        string                $supplierCode,
    ): JsonResponse
    {
        $this->supplierService->update(
            supplierCode: $supplierCode,
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Supplier berhasil diperbarui',
        );
    }
}
