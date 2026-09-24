<?php

namespace App\Http\Requests\Purchase;

use App\Enum\StoreType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_code' => [Rule::requiredIf($this->user()?->is_owner), 'nullable', 'string',
                Rule::exists('stores', 'store_code')->where('type', StoreType::CENTRAL->value)
                    ->where('is_active', true),
            ],
            'supplier_receipt_number' => ['required', 'string', 'max:255', Rule::unique('purchases', 'supplier_receipt_number')],
            'supplier_code' => ['nullable', 'string', Rule::exists('suppliers', 'supplier_code')],
            'purchase_date' => ['required', 'date_format:Y-m-d'],
            'payment_method' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string', Rule::exists('products', 'sku')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_code.required' => 'Toko central wajib dipilih.',
            'store_code.string' => 'Kode toko harus berupa teks.',
            'store_code.exists' => 'Toko central yang aktif tidak ditemukan.',

            'supplier_receipt_number.required' => 'Nomor struk pemasok wajib diisi.',
            'supplier_receipt_number.string' => 'Nomor struk pemasok harus berupa teks.',
            'supplier_receipt_number.max' => 'Nomor struk pemasok maksimal 255 karakter.',
            'supplier_receipt_number.unique' => 'Nomor struk pemasok sudah tercatat.',

            'supplier_code.string' => 'Kode pemasok harus berupa teks.',
            'supplier_code.exists' => 'Pemasok tidak ditemukan.',

            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'purchase_date.date_format' => 'Tanggal pembelian harus berformat YYYY-MM-DD.',

            'payment_method.required' => 'Metode pembayaran wajib diisi.',
            'payment_method.string' => 'Metode pembayaran harus berupa teks.',
            'payment_method.max' => 'Metode pembayaran maksimal 255 karakter.',

            'items.required' => 'Item pembelian wajib diisi.',
            'items.array' => 'Item pembelian harus berupa daftar.',
            'items.min' => 'Pembelian harus memiliki minimal satu item.',
            'items.*.sku.required' => 'Produk pada setiap item wajib dipilih.',
            'items.*.sku.string' => 'Kode produk harus berupa teks.',
            'items.*.sku.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah pada setiap item wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah item harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah item minimal 1.',
            'items.*.unit_price.required' => 'Harga beli pada setiap item wajib diisi.',
            'items.*.unit_price.integer' => 'Harga beli harus berupa bilangan bulat.',
            'items.*.unit_price.min' => 'Harga beli tidak boleh negatif.',
        ];
    }
}
