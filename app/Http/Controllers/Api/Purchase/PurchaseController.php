<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\CreatePurchaseRequest;
use App\Http\Requests\Purchase\PurchaseListRequest;
use App\Http\Resources\Purchase\PurchaseDetailResource;
use App\Http\Resources\Purchase\PurchaseListResource;
use App\Http\Service\Purchase\PurchaseService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
    )
    {
    }

    public function purchaseList(PurchaseListRequest $request): JsonResponse
    {
        $purchases = $this->purchaseService->paginate(
            $request->validated(),
            $request->user()
        );

        $data = PurchaseListResource::collection(
            $purchases->getCollection()
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar pembelian berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $purchases->currentPage(),
                'per_page' => $purchases->perPage(),
                'total_page' => $purchases->lastPage(),
                'total_data' => $purchases->total(),
            ],
        );
    }

    public function detail(Request $request, string $purchaseCode): JsonResponse
    {
        $purchase = $this->purchaseService->detail(
            $purchaseCode,
            $request->user()
        );

        $data = (new PurchaseDetailResource($purchase))->resolve($request);

        return ApiResponse::success(
            message: 'Detail pembelian berhasil diambil',
            data: $data,
        );
    }

    public function create(CreatePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->create(
            $request->validated(),
            $request->user()
        );

        return ApiResponse::success(
            message: 'Pembelian berhasil dicatat',
            data: ['purchase_code' => $purchase->purchase_code],
            statusCode: 201,
        );
    }
}
