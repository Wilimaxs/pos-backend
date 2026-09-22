<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
            'store_code' => ['bail', 'sometimes', 'required', 'string', Rule::exists('stores', 'store_code')
                ->where('is_active', true)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_code.required' => 'Store wajib dipilih.',
            'store_code.string' => 'Kode store harus berupa teks.',
            'store_code.exists' => 'Store tidak ditemukan atau sudah tidak aktif.',

            'name.required' => 'Nama pegawai wajib diisi.',
            'name.string' => 'Nama pegawai harus berupa teks.',
            'name.max' => 'Nama pegawai maksimal 255 karakter.',

            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',

            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 255 karakter.',

            'position.required' => 'Jabatan pegawai wajib diisi.',
            'position.string' => 'Jabatan pegawai harus berupa teks.',
            'position.max' => 'Jabatan pegawai maksimal 255 karakter.',

            'is_active.required' => 'Status aktif wajib diisi.',
            'is_active.boolean' => 'Status aktif harus berupa true atau false.',
        ];
    }
}
