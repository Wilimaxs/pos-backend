<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_code' => ['sometimes', 'required', 'string', Rule::exists('categories', 'category_code')
                ->where('is_active', true)],
            'barcode' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('products', 'barcode')
                ->ignore($this->route('sku'), 'sku')],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'unit' => ['sometimes', Rule::in(['PCS'])],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'stock_minimum' => ['sometimes', 'integer', 'min:0'],
            'selling_price' => ['sometimes', 'integer', 'min:0'],
            'store_code' => ['nullable', 'string', Rule::exists('stores', 'store_code')],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (!$this->hasAny([
                    'category_code', 'barcode', 'name', 'description', 'unit',
                    'is_active', 'stock_minimum', 'selling_price', 'image',
                ])) {
                    $validator->errors()->add('data', 'Minimal satu data harus diperbarui.');
                }

                if ($this->user()?->is_owner
                    && $this->hasAny(['stock_minimum', 'selling_price'])
                    && !$this->filled('store_code')) {
                    $validator->errors()->add('store_code', 'Toko tujuan wajib dipilih.');
                }
            },
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

            'image.image' => 'Gambar produk harus berupa file gambar.',
            'image.mimes' => 'Gambar produk harus berformat JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar produk maksimal 2 MB.',

            'unit.in' => 'Satuan produk harus PCS.',

            'is_active.required' => 'Status aktif produk wajib diisi.',
            'is_active.boolean' => 'Status aktif produk tidak valid.',

            'stock_minimum.integer' => 'Stok minimum harus berupa bilangan bulat.',
            'stock_minimum.min' => 'Stok minimum tidak boleh negatif.',

            'selling_price.integer' => 'Harga jual harus berupa bilangan bulat.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',

            'store_code.string' => 'Kode toko harus berupa teks.',
            'store_code.exists' => 'Toko tidak ditemukan.',
        ];
    }
}
