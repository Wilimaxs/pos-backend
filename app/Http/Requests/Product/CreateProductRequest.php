<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_code' => ['required', 'string', Rule::exists('categories', 'category_code')
                ->where('is_active', true)],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', Rule::in(['PCS'])],
            'is_active' => ['required', 'boolean'],
            'stock_minimum' => ['required', 'integer', 'min:0'],
            'selling_price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_code.required' => 'Kategori produk wajib dipilih.',
            'category_code.string' => 'Kode kategori harus berupa teks.',
            'category_code.exists' => 'Kategori produk tidak ditemukan atau tidak aktif.',

            'barcode.string' => 'Barcode harus berupa teks.',
            'barcode.max' => 'Barcode maksimal 255 karakter.',
            'barcode.unique' => 'Barcode sudah digunakan.',

            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 255 karakter.',

            'description.string' => 'Deskripsi produk harus berupa teks.',

            'unit.required' => 'Satuan produk wajib diisi.',
            'unit.in' => 'Satuan produk harus PCS.',

            'is_active.required' => 'Status aktif produk wajib diisi.',
            'is_active.boolean' => 'Status aktif produk tidak valid.',

            'stock_minimum.required' => 'Stok minimum wajib diisi.',
            'stock_minimum.integer' => 'Stok minimum harus berupa bilangan bulat.',
            'stock_minimum.min' => 'Stok minimum tidak boleh negatif.',

            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.integer' => 'Harga jual harus berupa bilangan bulat.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',
        ];
    }
}
