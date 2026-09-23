<?php

namespace App\Http\Service\Category;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryService
{
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Category::query()
            ->select([
                'category_code',
                'name',
                'description',
                'is_active',
            ])
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);

                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('category_code', 'like', "%$search%")
                            ->orWhere('name', 'like', "%$search%");
                    });
                }
            )
            ->when(
                ! is_null($filter['is_active'] ?? null),
                function ($query) use ($filter): void {
                    $query->where('is_active', $filter['is_active']);
                }
            )
            ->orderByDesc('created_at')
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(array $data): Category
    {
        return Category::query()->create($data);
    }

    public function options(): Collection
    {
        return Category::query()
            ->select([
                'categories.category_code',
                'categories.name',
            ])
            ->where('categories.is_active', true)
            ->orderBy('categories.name')
            ->get();
    }

    public function update(
        string $categoryCode,
        array $data,
    ): void {
        $category = Category::query()
            ->whereKey($categoryCode)
            ->firstOrFail();

        $category->update($data);
    }
}
