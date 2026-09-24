<?php

namespace Database\Seeders;

use App\Http\Service\Product\ProductService;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productService = app(ProductService::class);

        $products = [
            'Minuman' => [
                ['barcode' => '8999900000017', 'name' => 'Air Mineral 600 ml', 'selling_price' => 4000, 'stock_minimum' => 24],
                ['barcode' => '8999900000024', 'name' => 'Teh Botol 350 ml', 'selling_price' => 6000, 'stock_minimum' => 12],
                ['barcode' => '8999900000031', 'name' => 'Kopi Susu Kaleng 240 ml', 'selling_price' => 9000, 'stock_minimum' => 12],
            ],
            'Makanan Ringan' => [
                ['barcode' => '8999900000048', 'name' => 'Keripik Kentang 68 g', 'selling_price' => 11000, 'stock_minimum' => 10],
                ['barcode' => '8999900000055', 'name' => 'Biskuit Cokelat 120 g', 'selling_price' => 8500, 'stock_minimum' => 10],
                ['barcode' => '8999900000062', 'name' => 'Wafer Vanila 100 g', 'selling_price' => 7000, 'stock_minimum' => 10],
            ],
            'Kebutuhan Harian' => [
                ['barcode' => '8999900000079', 'name' => 'Sabun Mandi Batang 75 g', 'selling_price' => 5500, 'stock_minimum' => 12],
                ['barcode' => '8999900000086', 'name' => 'Tisu Wajah 180 Lembar', 'selling_price' => 12000, 'stock_minimum' => 8],
            ],
        ];

        foreach ($products as $categoryName => $items) {
            $category = Category::query()->firstOrCreate(
                ['name' => $categoryName],
                ['is_active' => true],
            );

            foreach ($items as $item) {
                if (Product::query()->where('barcode', $item['barcode'])->exists()) {
                    continue;
                }

                $productService->create([
                    'category_code' => $category->category_code,
                    'barcode' => $item['barcode'],
                    'name' => $item['name'],
                    'description' => null,
                    'unit' => 'PCS',
                    'is_active' => true,
                    'stock_minimum' => $item['stock_minimum'],
                    'selling_price' => $item['selling_price'],
                ]);
            }
        }
    }
}
