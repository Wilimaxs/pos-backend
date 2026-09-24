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
            'is_active' => ['nullable', 'boolean'],
            'category_code' => ['nullable', 'string', 'max:100', Rule::exists('categories', 'category_code')],
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
        ];
    }
}
