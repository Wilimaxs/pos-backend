<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'store_code' => ['nullable', 'string', Rule::exists('stores', 'store_code')],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Kata pencarian harus berupa teks.',
            'search.max' => 'Kata pencarian maksimal 100 karakter.',

            'store_code.string' => 'Kode toko harus berupa teks.',
            'store_code.exists' => 'Toko tidak ditemukan.',
        ];
    }
}
