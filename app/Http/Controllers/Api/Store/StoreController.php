<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\StoreOptionRequest;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Resources\Store\StoreOptionResource;
use App\Http\Resources\Store\StoreResource;
use App\Http\Service\Store\StoreService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(
        private readonly StoreService $storeService,
    )
    {
    }

    public function storeList(StoreRequest $request): JsonResponse
    {
        $stores = $this->storeService->paginate(
            filter: $request->validated(),
            actor: $request->user(),
        );

        $data = StoreResource::collection(
            $stores->getCollection(),
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar Store berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $stores->currentPage(),
                'per_page' => $stores->perPage(),
                'total_page' => $stores->lastPage(),
                'total_data' => $stores->total(),
            ]
        );
    }

    public function options(StoreOptionRequest $request): JsonResponse
    {
        $stores = $this->storeService->options(
            filter: $request->validated(),
            actor: $request->user(),
        );

        $data = StoreOptionResource::collection(
            $stores->getCollection(),
        )->resolve($request);

        return ApiResponse::success(
            message: 'Pilihan Store berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $stores->currentPage(),
                'per_page' => $stores->perPage(),
                'total_page' => $stores->lastPage(),
                'total_data' => $stores->total(),
            ]
        );
    }

    public function create(CreateStoreRequest $request): JsonResponse
    {
        $this->storeService->create(
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Store berhasil dibuat',
            statusCode: 201,
        );
    }

    public function update(
        UpdateStoreRequest $request,
        string             $storeCode,
    ): JsonResponse
    {
        $this->storeService->update(
            storeCode: $storeCode,
            data: $request->validated(),
            actor: $request->user(),
        );

        return ApiResponse::success(
            message: 'Store berhasil diperbarui',
        );
    }
}
