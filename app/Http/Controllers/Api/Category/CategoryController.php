<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryOptionResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Service\Category\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function categoryList(CategoryRequest $request): JsonResponse
    {
        $categories = $this->categoryService->paginate(
            filter: $request->validated(),
        );

        $data = CategoryResource::collection(
            $categories->getCollection(),
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar kategori berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total_page' => $categories->lastPage(),
                'total_data' => $categories->total(),
            ],
        );
    }

    public function options(Request $request): JsonResponse
    {
        $categories = $this->categoryService->options();

        $data = CategoryOptionResource::collection(
            $categories,
        )->resolve($request);

        return ApiResponse::success(
            message: 'Pilihan kategori berhasil diambil',
            data: $data,
        );
    }

    public function create(CreateCategoryRequest $request): JsonResponse
    {
        $this->categoryService->create(
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Kategori berhasil dibuat',
            statusCode: 201,
        );
    }

    public function update(
        UpdateCategoryRequest $request,
        string $categoryCode,
    ): JsonResponse {
        $this->categoryService->update(
            categoryCode: $categoryCode,
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Kategori berhasil diperbarui',
        );
    }
}
