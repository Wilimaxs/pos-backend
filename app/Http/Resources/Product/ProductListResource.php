<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sku' => $this->sku,
            'image_path' => $this->image_path,
            'name' => $this->name,
            'category_name' => $this->category?->name,
            'is_active' => $this->is_active,
        ];
    }
}
