<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category_code' => ['nullable', 'string', Rule::exists('categories', 'category_code')],
            'is_active' => ['nullable', 'boolean'],
            'store_code' => ['nullable', 'string', Rule::exists('stores', 'store_code')],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Kata pencarian harus berupa teks.',
            'search.max' => 'Kata pencarian maksimal 100 karakter.',

            'category_code.string' => 'Kode kategori harus berupa teks.',
            'category_code.exists' => 'Kategori produk tidak ditemukan.',

            'is_active.boolean' => 'Status aktif produk tidak valid.',

            'store_code.string' => 'Kode toko harus berupa teks.',
            'store_code.exists' => 'Toko tidak ditemukan.',
        ];
    }
}
