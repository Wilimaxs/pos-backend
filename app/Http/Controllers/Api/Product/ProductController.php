<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\ProductListRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Service\Product\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    )
    {
    }

    public function productList(ProductListRequest $request): JsonResponse
    {
        $products = $this->productService->paginate(
            filter: $request->validated(),
            actor: $request->user(),
        );

        $data = ProductResource::collection(
            $products->getCollection()
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar produk berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total_page' => $products->lastPage(),
                'total_data' => $products->total(),
            ],
        );
    }

    public function create(CreateProductRequest $request): JsonResponse
    {
        $this->productService->create(
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Produk berhasil dibuat',
            statusCode: 201,
        );
    }

    public function update(UpdateProductRequest $request, string $sku): JsonResponse
    {
        $this->productService->update(
            sku: $sku,
            data: $request->validated(),
            actor: $request->user(),
        );

        return ApiResponse::success(
            message: 'Produk berhasil diperbarui',
        );
    }
}
