<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'store_code' => ['bail', 'nullable', 'string', Rule::exists('stores', 'store_code')],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Pencarian harus berupa teks.',
            'search.max' => 'Pencarian maksimal 50 karakter.',

            'is_active.boolean' => 'Status aktif harus bernilai true atau false.',

            'store_code.string' => 'Kode store harus berupa teks.',
            'store_code.exists' => 'Store tidak ditemukan.',
        ];
    }
}
